<?php

namespace App\Services;

class MediaAttachmentService
{
    public function get($pid, $type, $oid, $path)
    {
        $id = HashidService::encode((string) $pid);
        $obid = HashidService::encode((string) $oid);
        $dhsh = HashidService::encode((string) now()->format('ym'));
        $paid = HashidService::encode((string) $path);
        $base = "{$type}/{$id}/{$dhsh}/$obid/$paid";

        return app(BunnyStorageService::class)->url($base);
    }
}
