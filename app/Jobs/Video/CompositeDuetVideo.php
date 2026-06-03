<?php

namespace App\Jobs\Video;

use App\Models\Video;
use App\Services\BunnyStorageService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class CompositeDuetVideo implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public $tries = 3;

    protected Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(): void
    {
        $tmpDir = null;

        try {
            $this->video->refresh();

            $originalLoop = Video::published()
                ->where('can_duet', true)
                ->find($this->video->original_duet_id);

            if (! $originalLoop || ! $originalLoop->vid_optimized || ! $this->video->vid) {
                throw new \Exception('Missing original or upload paths for duet.');
            }

            $storage = app(BunnyStorageService::class);
            $composedVideoPath = str_replace('.mp4', '_duet.1080p.mp4', $this->video->vid);
            $layout = $this->video->duet_layout == 1 ? 'side-by-side' : 'vertical';

            $tmpDir = storage_path('app/duet-processing/'.$this->video->id.'-'.uniqid());
            mkdir($tmpDir, 0755, true);

            $originalPath = $storage->downloadToTemp($originalLoop->vid_optimized, 'duet-original-');
            $responsePath = $storage->downloadToTemp($this->video->vid, 'duet-response-');
            $outputPath = $tmpDir.'/duet.mp4';

            try {
                $this->composeLocal($originalPath, $responsePath, $outputPath, $layout);
                $storage->putFile($composedVideoPath, $outputPath, 'video/mp4');

                $this->video->update([
                    'vid_optimized' => $composedVideoPath,
                    'duration' => $this->duration($outputPath),
                    'has_processed' => true,
                    'status' => 2,
                ]);
            } finally {
                @unlink($originalPath);
                @unlink($responsePath);
            }
        } catch (\Throwable $e) {
            Log::error("Duet composition failed for duet ID: {$this->video->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } finally {
            if ($tmpDir && is_dir($tmpDir)) {
                $this->cleanupDirectory($tmpDir);
            }
        }
    }

    private function composeLocal(string $originalPath, string $responsePath, string $outputPath, string $layout): void
    {
        $filter = $layout === 'side-by-side'
            ? '[0:v]scale=360:-2,setsar=1,pad=360:640:(ow-iw)/2:(oh-ih)/2[left];'.
                '[1:v]scale=360:-2,setsar=1,pad=360:640:(ow-iw)/2:(oh-ih)/2[right];'.
                '[left][right]hstack=inputs=2[v];'.
                '[0:a]volume=0.5[a0];[1:a]volume=1.0[a1];[a0][a1]amix=inputs=2:duration=shortest[a]'
            : '[0:v]scale=-2:960:force_original_aspect_ratio=decrease,'.
                'pad=1080:960:(ow-iw)/2:(oh-ih)/2:color=black,setsar=1[top];'.
                '[1:v]scale=-2:960:force_original_aspect_ratio=decrease,'.
                'pad=1080:960:(ow-iw)/2:(oh-ih)/2:color=black,setsar=1[bottom];'.
                '[top][bottom]vstack=inputs=2[v];'.
                '[0:a]volume=0.5[a0];[1:a]volume=1.0[a1];[a0][a1]amix=inputs=2:duration=shortest[a]';

        $cmd = implode(' ', [
            escapeshellarg((string) config('laravel-ffmpeg.ffmpeg.binaries', 'ffmpeg')),
            '-y',
            '-i', escapeshellarg($originalPath),
            '-i', escapeshellarg($responsePath),
            '-filter_complex', escapeshellarg($filter),
            '-map', escapeshellarg('[v]'),
            '-map', escapeshellarg('[a]'),
            '-c:v', 'libx264',
            '-c:a', 'aac',
            '-b:a', '128k',
            '-preset', 'slow',
            '-crf', '23',
            '-pix_fmt', 'yuv420p',
            '-movflags', '+faststart',
            '-ac', '2',
            '-shortest',
            escapeshellarg($outputPath),
        ]);

        $result = Process::timeout(600)->run($cmd);
        if ($result->failed()) {
            throw new \Exception('FFmpeg duet composition failed: '.$result->errorOutput());
        }
    }

    private function duration(string $path): int
    {
        $cmd = implode(' ', [
            escapeshellarg((string) config('laravel-ffmpeg.ffprobe.binaries', 'ffprobe')),
            '-v', 'error',
            '-show_entries', 'format=duration',
            '-of', 'default=noprint_wrappers=1:nokey=1',
            escapeshellarg($path),
        ]);

        $result = Process::timeout(60)->run($cmd);
        if ($result->failed()) {
            return 0;
        }

        return max(0, (int) round((float) trim($result->output())));
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
        Log::error("Duet composition job failed permanently for duet ID: {$this->video->id}", [
            'error' => $exception->getMessage(),
        ]);
    }
}
