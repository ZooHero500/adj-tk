<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BunnyStorageService
{
    public function configured(): bool
    {
        return filled(config('services.bunny_storage.endpoint'))
            && filled(config('services.bunny_storage.access_key'));
    }

    public function put(string $path, string $contents, ?string $contentType = null): void
    {
        $this->ensureConfigured();
        $this->sendPut($path, $contents, $contentType ?? $this->contentTypeFor($path));
    }

    public function putFile(string $path, string $localPath, ?string $contentType = null): void
    {
        $this->ensureConfigured();
        $stream = fopen($localPath, 'rb');
        if (! $stream) {
            throw new RuntimeException("Unable to open file for Bunny upload: {$localPath}");
        }

        try {
            $this->sendPut($path, $stream, $contentType ?? $this->contentTypeFor($path, $localPath));
        } finally {
            fclose($stream);
        }
    }

    public function url(string $path): string
    {
        $cdnUrl = rtrim((string) config('services.bunny_storage.cdn_url'), '/');
        if ($cdnUrl === '') {
            throw new RuntimeException('Bunny CDN URL is not configured');
        }

        return $cdnUrl.'/'.$this->encodePath($path);
    }

    public function exists(string $path): bool
    {
        $this->ensureConfigured();
        $response = $this->request()
            ->withHeaders(['Range' => 'bytes=0-0'])
            ->get($this->storageUrl($path));

        if ($response->status() === 404) {
            return false;
        }

        if (! $response->successful() && $response->status() !== 206) {
            $this->throwStorageError('Bunny exists check failed', $path, $response);
        }

        return true;
    }

    public function get(string $path): string
    {
        $this->ensureConfigured();
        $response = $this->request()->get($this->storageUrl($path));

        if (! $response->successful()) {
            $this->throwStorageError('Bunny download failed', $path, $response);
        }

        return $response->body();
    }

    public function downloadToTemp(string $path, string $prefix = 'bunny-'): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), $prefix);
        if (! $tempPath) {
            throw new RuntimeException('Unable to create temporary file for Bunny download');
        }

        file_put_contents($tempPath, $this->get($path));

        return $tempPath;
    }

    public function delete(string $path): void
    {
        $this->ensureConfigured();
        $response = $this->request()->delete($this->storageUrl($path));

        if (! $response->successful() && $response->status() !== 404) {
            $this->throwStorageError('Bunny delete failed', $path, $response);
        }
    }

    public function deleteDirectory(string $prefix): void
    {
        $this->ensureConfigured();
        $prefix = trim($prefix, '/');
        if ($prefix === '') {
            return;
        }

        $response = $this->request()->delete($this->storageUrl($prefix).'/');

        if (! $response->successful() && $response->status() !== 404) {
            $this->throwStorageError('Bunny delete directory failed', $prefix, $response);
        }
    }

    private function ensureConfigured(): void
    {
        if (! $this->configured()) {
            throw new RuntimeException('Bunny storage is not configured');
        }
    }

    private function sendPut(string $path, mixed $body, string $contentType): void
    {
        $response = $this->request()
            ->withHeaders([
                'Content-Type' => $contentType,
            ])
            ->send('PUT', $this->storageUrl($path), ['body' => $body]);

        if (! $response->successful()) {
            $this->throwUploadError($path, $response);
        }
    }

    private function storageUrl(string $path): string
    {
        return rtrim((string) config('services.bunny_storage.endpoint'), '/').'/'.$this->encodePath($path);
    }

    private function request(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout(300)
            ->retry(3, 500, throw: false)
            ->withHeaders([
                'AccessKey' => (string) config('services.bunny_storage.access_key'),
            ]);
    }

    private function encodePath(string $path): string
    {
        return collect(explode('/', ltrim($path, '/')))
            ->map(fn (string $segment) => rawurlencode($segment))
            ->implode('/');
    }

    private function contentTypeFor(string $path, ?string $localPath = null): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'mp4' => 'video/mp4',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'm3u8' => 'application/vnd.apple.mpegurl',
            'ts' => 'video/mp2t',
            default => $this->detectMimeType($localPath),
        };
    }

    private function detectMimeType(?string $localPath): string
    {
        if ($localPath && function_exists('mime_content_type')) {
            $mimeType = mime_content_type($localPath);
            if ($mimeType) {
                return $mimeType;
            }
        }

        return 'application/octet-stream';
    }

    private function throwUploadError(string $path, Response $response): never
    {
        $this->throwStorageError('Bunny upload failed', $path, $response);
    }

    private function throwStorageError(string $message, string $path, Response $response): never
    {
        $body = substr($response->body(), 0, 500);

        throw new RuntimeException("{$message} for {$path}: {$response->status()} {$body}");
    }
}
