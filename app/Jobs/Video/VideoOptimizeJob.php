<?php

namespace App\Jobs\Video;

use App\Services\BunnyStorageService;
use App\Services\VideoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class VideoOptimizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public $timeout = 300;

    public $tries = 3;

    public $maxExceptions = 3;

    public $deleteWhenMissingModels = true;

    public function __construct($video)
    {
        $this->video = $video->withoutRelations();
        $this->onQueue('video-processing');
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('video-processing:'.$this->video->id))->expireAfter(420)];
    }

    public function handle(): void
    {
        $video = $this->video->fresh();

        if (! $video) {
            Log::warning('Video not found for optimization job', ['video_id' => $this->video->id]);

            return;
        }

        if (str_starts_with($video->vid, 'https://')) {
            return;
        }

        $storage = app(BunnyStorageService::class);
        $tmpDir = null;

        try {
            if (! $storage->exists($video->vid)) {
                throw new \Exception('Source video file not found in storage: '.$video->vid);
            }

            $maxDuration = 180;
            $ext = pathinfo($video->vid, PATHINFO_EXTENSION);
            $name = str_replace('.'.$ext, '.720p.mp4', $video->vid);

            if ($video->vid_optimized || $storage->exists($name)) {
                $video->vid_optimized = $video->vid_optimized ?: $name;
                $video->has_processed = true;
                $video->status = 2;
                $video->save();

                return;
            }

            $tmpDir = storage_path('app/video-processing/'.$video->id.'-'.uniqid());
            if (! is_dir($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }

            $tmpInput = $tmpDir.'/input.mp4';
            $tmpOptimized = $tmpDir.'/optimized.mp4';
            $downloadedInput = $storage->downloadToTemp($video->vid, 'video-optimize-input-');
            copy($downloadedInput, $tmpInput);
            @unlink($downloadedInput);

            $sourceProbe = $this->probe($tmpInput);
            $videoStream = $this->firstStream($sourceProbe, 'video');

            if (! $videoStream) {
                throw new \Exception('Could not read video stream from file');
            }

            $hasAudio = $this->firstStream($sourceProbe, 'audio') !== null;
            $width = (int) ($videoStream['width'] ?? 0);
            $height = (int) ($videoStream['height'] ?? 0);

            if (! $width || ! $height) {
                throw new \Exception('Could not determine video dimensions');
            }

            if ($height > $width) {
                $scaleFilter = 'scale=720:-2';
                $maxBitrate = '2500k';
                $bufSize = '5000k';
                $video->width = 720;
                $video->height = (int) round($height * (720 / $width) / 2) * 2;
            } elseif ($width > $height) {
                $scaleFilter = 'scale=-2:720';
                $maxBitrate = '3000k';
                $bufSize = '6000k';
                $video->width = (int) round($width * (720 / $height) / 2) * 2;
                $video->height = 720;
            } else {
                $scaleFilter = 'scale=720:720';
                $maxBitrate = '2500k';
                $bufSize = '5000k';
                $video->width = 720;
                $video->height = 720;
            }

            $cmd = implode(' ', [
                escapeshellarg($this->ffmpegBin()),
                '-y',
                '-fflags', '+genpts',
                '-err_detect', 'ignore_err',
                '-i', escapeshellarg($tmpInput),
                '-vf', escapeshellarg($scaleFilter.',format=yuv420p'),
                '-sws_flags', 'lanczos',
                '-c:v', 'libx264',
                '-preset', 'slow',
                '-crf', '23',
                '-maxrate', escapeshellarg($maxBitrate),
                '-bufsize', escapeshellarg($bufSize),
                '-profile:v', 'high',
                '-level', '4.1',
                '-movflags', '+faststart',
                '-pix_fmt', 'yuv420p',
                '-tune', 'film',
                '-c:a', 'aac',
                '-b:a', '128k',
                '-ac', '2',
                '-t', (string) $maxDuration,
                escapeshellarg($tmpOptimized),
            ]);

            $result = Process::timeout(300)->run($cmd);
            if ($result->failed()) {
                throw new \Exception('FFmpeg optimization failed: '.$result->errorOutput());
            }

            $storage->putFile($name, $tmpOptimized, 'video/mp4');

            if (! $storage->exists($name)) {
                throw new \Exception('Optimized video was not created in storage');
            }

            $optimizedProbe = $this->probe($tmpOptimized);

            $video->duration = $this->durationFromProbe($optimizedProbe);
            $video->vid_optimized = $name;
            $video->has_processed = true;
            $video->has_audio = $hasAudio;
            $video->status = 2;

            $hlsGenerated = $this->generateHls($storage, $video, $tmpOptimized, $name, $tmpDir);
            $video->has_hls = $hlsGenerated;

            $video->save();

            VideoService::deleteMediaData($video->id);
        } catch (\Exception $e) {
            Log::error('Video optimization failed', [
                'video_id' => $video->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($this->attempts() >= $this->tries) {
                $video->processing_error = 'Optimization failed: '.$e->getMessage();
                $video->processing_status = 'failed';
                $video->processing_failed_at = now();
                $video->save();

                return;
            }

            throw $e;
        } finally {
            if ($tmpDir && is_dir($tmpDir)) {
                $this->cleanupDirectory($tmpDir);
            }
        }
    }

    private function generateHls(BunnyStorageService $storage, $video, string $optimizedLocalPath, string $optimizedPath, string $tmpDir): bool
    {
        try {
            $dir480 = $tmpDir.'/480p';
            $dir720 = $tmpDir.'/720p';
            mkdir($dir480, 0755, true);
            mkdir($dir720, 0755, true);

            $cmd480 = implode(' ', [
                escapeshellarg($this->ffmpegBin()), '-y', '-i', escapeshellarg($optimizedLocalPath),
                '-vf', 'scale=-2:480', '-c:v', 'libx264', '-preset', 'fast',
                '-crf', '28', '-c:a', 'aac', '-b:a', '96k', '-ac', '2',
                '-f', 'hls', '-hls_time', '6', '-hls_list_size', '0',
                '-hls_segment_filename', escapeshellarg($dir480.'/seg_%03d.ts'),
                escapeshellarg($dir480.'/playlist.m3u8'),
            ]);

            $cmd720 = implode(' ', [
                escapeshellarg($this->ffmpegBin()), '-y', '-i', escapeshellarg($optimizedLocalPath),
                '-vf', 'scale=-2:720', '-c:v', 'libx264', '-preset', 'fast',
                '-crf', '23', '-c:a', 'aac', '-b:a', '128k', '-ac', '2',
                '-f', 'hls', '-hls_time', '6', '-hls_list_size', '0',
                '-hls_segment_filename', escapeshellarg($dir720.'/seg_%03d.ts'),
                escapeshellarg($dir720.'/playlist.m3u8'),
            ]);

            $result480 = Process::timeout(300)->run($cmd480);
            if ($result480->failed()) {
                Log::warning('HLS: 480p encoding failed', ['video_id' => $video->id, 'error' => $result480->errorOutput()]);

                return false;
            }

            $result720 = Process::timeout(300)->run($cmd720);
            if ($result720->failed()) {
                Log::warning('HLS: 720p encoding failed', ['video_id' => $video->id, 'error' => $result720->errorOutput()]);

                return false;
            }

            $hlsBase = pathinfo($optimizedPath, PATHINFO_DIRNAME);
            $master = "#EXTM3U\n";
            $master .= "#EXT-X-STREAM-INF:BANDWIDTH=800000,RESOLUTION=854x480\n";
            $master .= "480p/playlist.m3u8\n";
            $master .= "#EXT-X-STREAM-INF:BANDWIDTH=2500000,RESOLUTION=1280x720\n";
            $master .= "720p/playlist.m3u8\n";

            foreach (['480p', '720p'] as $variant) {
                $variantDir = $tmpDir.'/'.$variant;
                foreach (glob($variantDir.'/*') as $file) {
                    $storage->putFile($hlsBase.'/'.$variant.'/'.basename($file), $file);
                }
            }

            $storage->put($hlsBase.'/master.m3u8', $master, 'application/vnd.apple.mpegurl');

            Log::info('HLS: Generated successfully', ['video_id' => $video->id, 'path' => $hlsBase]);

            return true;
        } catch (\Exception $e) {
            Log::warning('HLS: Generation failed', ['video_id' => $video->id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    private function probe(string $path): array
    {
        $cmd = implode(' ', [
            escapeshellarg($this->ffprobeBin()),
            '-v', 'error',
            '-print_format', 'json',
            '-show_format',
            '-show_streams',
            escapeshellarg($path),
        ]);

        $result = Process::timeout(60)->run($cmd);
        if ($result->failed()) {
            throw new \Exception('FFprobe failed: '.$result->errorOutput());
        }

        $probe = json_decode($result->output(), true);
        if (! is_array($probe)) {
            throw new \Exception('FFprobe returned invalid JSON');
        }

        return $probe;
    }

    private function firstStream(array $probe, string $codecType): ?array
    {
        foreach (($probe['streams'] ?? []) as $stream) {
            if (($stream['codec_type'] ?? null) === $codecType) {
                return $stream;
            }
        }

        return null;
    }

    private function durationFromProbe(array $probe): int
    {
        $duration = (float) data_get($probe, 'format.duration', 0);

        return max(0, (int) round($duration));
    }

    private function ffmpegBin(): string
    {
        return (string) config('laravel-ffmpeg.ffmpeg.binaries', 'ffmpeg');
    }

    private function ffprobeBin(): string
    {
        return (string) config('laravel-ffmpeg.ffprobe.binaries', 'ffprobe');
    }

    private function cleanupDirectory(string $dir): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }

        rmdir($dir);
    }

    public function failed(\Throwable $exception): void
    {
        $video = $this->video->fresh();

        if ($video) {
            $video->processing_status = 'failed';
            $video->processing_error = 'Optimization: '.$exception->getMessage();
            $video->processing_failed_at = now();
            $video->save();
        }

        Log::error('Video optimization job permanently failed', [
            'video_id' => $this->video->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
