<?php

namespace App\Jobs\Video;

use App\Services\VideoService;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

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

        try {
            if (! Storage::disk('s3')->exists($video->vid)) {
                throw new \Exception('Source video file not found on S3: '.$video->vid);
            }

            $maxDuration = 180;
            $ext = pathinfo($video->vid, PATHINFO_EXTENSION);
            $name = str_replace('.'.$ext, '.720p.mp4', $video->vid);

            if ($video->vid_optimized || Storage::disk('s3')->exists($name)) {
                $video->has_processed = true;
                $video->status = 2;
                $video->save();

                return;
            }

            $mediaInfo = FFMpeg::fromDisk('s3')->open($video->vid);
            $videoStream = $mediaInfo->getVideoStream();

            if (! $videoStream) {
                throw new \Exception('Could not read video stream from file');
            }

            $hasAudio = false;
            try {
                $audioStream = $mediaInfo->getAudioStream();
                $hasAudio = $audioStream !== null;
            } catch (\Exception $e) {
                $hasAudio = false;
            }

            $width = $videoStream->get('width');
            $height = $videoStream->get('height');

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

            $format = new X264('aac');
            $format
                ->setAudioKiloBitrate(128)
                ->setKiloBitrate(0)
                ->setAdditionalParameters([
                    '-preset', 'slow',
                    '-crf', '23',
                    '-maxrate', $maxBitrate,
                    '-bufsize', $bufSize,
                    '-nal-hrd', 'vbr',
                    '-profile:v', 'high',
                    '-level', '4.1',
                    '-movflags', '+faststart',
                    '-pix_fmt', 'yuv420p',
                    '-tune', 'film',
                    '-ac', '2',
                    '-t', (string) $maxDuration,
                ]);

            $media = FFMpeg::fromDisk('s3')
                ->open($video->vid)
                ->addFilter(['-vf', $scaleFilter.',format=yuv420p'])
                ->addFilter('-sws_flags', 'lanczos')
                ->addFilter('-err_detect', 'ignore_err')
                ->addFilter('-fflags', '+genpts')
            // @phpstan-ignore-next-line
                ->export()
                ->toDisk('s3')
                ->inFormat($format)
                ->withVisibility('public')
                ->save($name);

            // @phpstan-ignore-next-line
            if (! Storage::disk('s3')->exists($name)) {
                throw new \Exception('Optimized video was not created on S3');
            }

            // @phpstan-ignore-next-line
            $video->duration = $media->getDurationInSeconds();
            $video->vid_optimized = $name;
            $video->has_processed = true;
            $video->has_audio = (bool) $hasAudio;
            $video->status = 2;

            // Generate HLS segments
            $hlsGenerated = $this->generateHls($video, $name);
            $video->has_hls = $hlsGenerated;

            $video->save();

            $media->cleanupTemporaryFiles();
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
        }
    }

    private function generateHls($video, string $optimizedPath): bool
    {
        try {
            $tmpDir = storage_path('app/hls-tmp/'.$video->id);
            $tmpInput = $tmpDir.'/input.mp4';

            if (! is_dir($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }

            // Download the optimized MP4 from S3
            $stream = Storage::disk('s3')->readStream($optimizedPath);
            if (! $stream) {
                Log::warning('HLS: Could not read optimized video from S3', ['video_id' => $video->id]);

                return false;
            }
            file_put_contents($tmpInput, $stream);

            $dir480 = $tmpDir.'/480p';
            $dir720 = $tmpDir.'/720p';
            mkdir($dir480, 0755, true);
            mkdir($dir720, 0755, true);

            // 480p variant
            $cmd480 = implode(' ', [
                'ffmpeg', '-y', '-i', escapeshellarg($tmpInput),
                '-vf', 'scale=-2:480', '-c:v', 'libx264', '-preset', 'fast',
                '-crf', '28', '-c:a', 'aac', '-b:a', '96k', '-ac', '2',
                '-f', 'hls', '-hls_time', '6', '-hls_list_size', '0',
                '-hls_segment_filename', escapeshellarg($dir480.'/seg_%03d.ts'),
                escapeshellarg($dir480.'/playlist.m3u8'),
            ]);

            // 720p variant
            $cmd720 = implode(' ', [
                'ffmpeg', '-y', '-i', escapeshellarg($tmpInput),
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

            // Build master playlist
            $basePath = pathinfo($optimizedPath, PATHINFO_DIRNAME);
            $hlsBase = $basePath.'/hls_'.$video->id;

            $master = "#EXTM3U\n";
            $master .= "#EXT-X-STREAM-INF:BANDWIDTH=800000,RESOLUTION=854x480\n";
            $master .= "480p/playlist.m3u8\n";
            $master .= "#EXT-X-STREAM-INF:BANDWIDTH=2500000,RESOLUTION=1280x720\n";
            $master .= "720p/playlist.m3u8\n";

            // Upload all HLS files to S3
            foreach (['480p', '720p'] as $variant) {
                $variantDir = $tmpDir.'/'.$variant;
                foreach (glob($variantDir.'/*') as $file) {
                    $s3Key = $hlsBase.'/'.$variant.'/'.basename($file);
                    Storage::disk('s3')->put($s3Key, file_get_contents($file), 'public');
                }
            }

            // Upload master playlist
            Storage::disk('s3')->put($hlsBase.'/master.m3u8', $master, 'public');

            // Clean up tmp files
            $this->cleanupDirectory($tmpDir);

            Log::info('HLS: Generated successfully', ['video_id' => $video->id, 'path' => $hlsBase]);

            return true;
        } catch (\Exception $e) {
            Log::warning('HLS: Generation failed', ['video_id' => $video->id, 'error' => $e->getMessage()]);

            if (isset($tmpDir) && is_dir($tmpDir)) {
                $this->cleanupDirectory($tmpDir);
            }

            return false;
        }
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
