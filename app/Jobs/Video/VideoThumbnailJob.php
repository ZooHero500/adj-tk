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
use Illuminate\Support\Str;

class VideoThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public $timeout = 120;

    public $tries = 3;

    public $maxExceptions = 3;

    public $deleteWhenMissingModels = true;

    public $backoff = [10, 30, 60];

    public function __construct($video)
    {
        $this->video = $video->withoutRelations();
        $this->onQueue('video-processing');
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('video-thumb:'.$this->video->id))->expireAfter(180)];
    }

    public function handle(): void
    {
        $video = $this->video->fresh();

        if (! $video) {
            Log::warning('Video not found for thumbnail job', ['video_id' => $this->video->id]);

            return;
        }

        if (str_starts_with($video->vid, 'https://')) {
            return;
        }

        try {
            $storage = app(BunnyStorageService::class);

            if (! $storage->exists($video->vid)) {
                throw new \Exception('Video file not found in storage: '.$video->vid);
            }

            $ext = pathinfo($video->vid, PATHINFO_EXTENSION);
            $randomStr = Str::random(8);
            $thumb = str_replace('.'.$ext, '_thumb_'.$randomStr.'.jpg', $video->vid);

            if ($storage->exists($thumb)) {
                $video->thumbnail_path = $thumb;
                $video->has_thumb = true;
                $video->save();
                VideoService::deleteMediaData($video->id);

                return;
            }

            $tmpInput = $storage->downloadToTemp($video->vid, 'video-thumb-input-');
            $tmpThumb = tempnam(sys_get_temp_dir(), 'video-thumb-output-').'.jpg';

            try {
                $cmd = implode(' ', [
                    escapeshellarg((string) config('laravel-ffmpeg.ffmpeg.binaries', 'ffmpeg')),
                    '-y',
                    '-ss', '0',
                    '-i', escapeshellarg($tmpInput),
                    '-frames:v', '1',
                    '-q:v', '2',
                    escapeshellarg($tmpThumb),
                ]);

                $result = Process::timeout(120)->run($cmd);
                if ($result->failed()) {
                    throw new \Exception('FFmpeg thumbnail generation failed: '.$result->errorOutput());
                }

                $storage->putFile($thumb, $tmpThumb, 'image/jpeg');
            } finally {
                @unlink($tmpInput);
                @unlink($tmpThumb);
            }

            if (! $storage->exists($thumb)) {
                throw new \Exception('Thumbnail was not created in storage');
            }

            $video->thumbnail_path = $thumb;
            $video->has_thumb = true;
            $video->save();

            VideoService::deleteMediaData($video->id);
        } catch (\Exception $e) {
            Log::error('Video thumbnail generation failed', [
                'video_id' => $video->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($this->attempts() >= $this->tries) {
                $video->processing_error = 'Thumbnail generation failed: '.$e->getMessage();
                $video->processing_status = 'failed';
                $video->processing_failed_at = now();
                $video->save();
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $video = $this->video->fresh();

        if ($video) {
            $video->processing_status = 'failed';
            $video->processing_error = 'Thumbnail: '.$exception->getMessage();
            $video->processing_failed_at = now();
            $video->save();
        }

        Log::error('Video thumbnail job permanently failed', [
            'video_id' => $this->video->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
