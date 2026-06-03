<?php

namespace Tests\Feature;

use App\Services\BunnyStorageService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BunnyStorageServiceTest extends TestCase
{
    public function test_it_uploads_to_bunny_storage_http_api(): void
    {
        config()->set('services.bunny_storage.endpoint', 'https://storage.bunnycdn.com/adjtk');
        config()->set('services.bunny_storage.access_key', 'test-key');
        config()->set('services.bunny_storage.cdn_url', 'https://porntk.b-cdn.net');

        Http::fake([
            'storage.bunnycdn.com/adjtk/videos/test.mp4' => Http::response('', 201),
        ]);

        app(BunnyStorageService::class)->put('videos/test.mp4', 'video-bytes');

        Http::assertSent(function ($request) {
            return $request->method() === 'PUT'
                && $request->url() === 'https://storage.bunnycdn.com/adjtk/videos/test.mp4'
                && $request->hasHeader('AccessKey', 'test-key')
                && $request->hasHeader('Content-Type', 'video/mp4')
                && $request->body() === 'video-bytes';
        });
    }

    public function test_it_generates_bunny_cdn_urls(): void
    {
        config()->set('services.bunny_storage.endpoint', 'https://storage.bunnycdn.com/adjtk');
        config()->set('services.bunny_storage.access_key', 'test-key');
        config()->set('services.bunny_storage.cdn_url', 'https://porntk.b-cdn.net');

        $url = app(BunnyStorageService::class)->url('v/hello world/video.mp4');

        $this->assertSame('https://porntk.b-cdn.net/v/hello%20world/video.mp4', $url);
    }

    public function test_it_checks_and_reads_and_deletes_bunny_objects(): void
    {
        config()->set('services.bunny_storage.endpoint', 'https://storage.bunnycdn.com/adjtk');
        config()->set('services.bunny_storage.access_key', 'test-key');

        Http::fake([
            'storage.bunnycdn.com/adjtk/videos/test.mp4' => Http::sequence()
                ->push('', 200)
                ->push('video-bytes', 200)
                ->push('', 200),
        ]);

        $storage = app(BunnyStorageService::class);

        $this->assertTrue($storage->exists('videos/test.mp4'));
        $this->assertSame('video-bytes', $storage->get('videos/test.mp4'));
        $storage->delete('videos/test.mp4');

        Http::assertSent(function ($request) {
            return $request->method() === 'GET'
                && $request->hasHeader('Range', 'bytes=0-0');
        });
        Http::assertSentCount(3);
    }

    public function test_it_downloads_bunny_objects_to_temp_files(): void
    {
        config()->set('services.bunny_storage.endpoint', 'https://storage.bunnycdn.com/adjtk');
        config()->set('services.bunny_storage.access_key', 'test-key');

        Http::fake([
            'storage.bunnycdn.com/adjtk/videos/test.mp4' => Http::response('video-bytes', 200),
        ]);

        $localPath = app(BunnyStorageService::class)->downloadToTemp('videos/test.mp4', 'video-');

        $this->assertFileExists($localPath);
        $this->assertSame('video-bytes', file_get_contents($localPath));

        unlink($localPath);
    }

    public function test_exists_returns_false_for_missing_bunny_objects(): void
    {
        config()->set('services.bunny_storage.endpoint', 'https://storage.bunnycdn.com/adjtk');
        config()->set('services.bunny_storage.access_key', 'test-key');

        Http::fake([
            'storage.bunnycdn.com/adjtk/videos/missing.mp4' => Http::response('', 404),
        ]);

        $this->assertFalse(app(BunnyStorageService::class)->exists('videos/missing.mp4'));
    }

    public function test_it_fails_when_bunny_storage_is_not_configured(): void
    {
        config()->set('services.bunny_storage.endpoint', null);
        config()->set('services.bunny_storage.access_key', null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Bunny storage is not configured');

        app(BunnyStorageService::class)->put('videos/test.mp4', 'video-bytes');
    }
}
