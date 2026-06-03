<?php

namespace App\Jobs\Video;

use App\Models\Video;
use App\Services\BunnyStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class VideoCustomThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 10;

    public function __construct(public Video $video) {}

    public function handle(): void
    {
        if (! $this->video->thumbnail_path) {
            return;
        }

        $storage = app(BunnyStorageService::class);

        try {
            $originalPath = $this->video->thumbnail_path;
            $contents = $storage->get($originalPath);

            if (! $contents) {
                throw new \Exception('Failed to read thumbnail from storage');
            }

            $image = Image::read($contents);
            $image->cover(1080, 1920);

            $tempPath = sys_get_temp_dir().'/'.uniqid($this->video->id.'_thumb_').'.webp';
            $image->toWebp(quality: 95, strip: true)->save($tempPath);

            $pid = $this->video->profile_id;
            $fileName = 'thumb_'.Str::random(8).'.webp';
            $storagePath = 'videos/'.$pid.'/'.$this->video->id.'/'.$fileName;

            $storage->putFile($storagePath, $tempPath, 'image/webp');

            unlink($tempPath);

            if ($originalPath !== $storagePath) {
                $storage->delete($originalPath);
            }

            $this->video->has_thumb = true;
            $this->video->thumbnail_path = $storagePath;
            $this->video->thumbnail_width = 1080;
            $this->video->thumbnail_height = 1920;
            $this->video->thumbnail_mime = 'image/webp';
            $this->video->save();

        } catch (\Exception $e) {
            if (config('logging.dev_log')) {
                Log::error('Custom thumbnail optimization failed', [
                    'video_id' => $this->video->id,
                    'error' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }
}
