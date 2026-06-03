<?php

namespace App\Models;

use App\Services\BunnyStorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileAvatar extends Model
{
    protected $fillable = [
        'profile_id',
        'remote_url',
        'path',
        'mime',
        'size',
        'skip_refetch',
        'is_invalid',
        'last_fetched_at',
    ];

    protected $casts = [
        'skip_refetch' => 'boolean',
        'is_invalid' => 'boolean',
        'last_fetched_at' => 'datetime',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? app(BunnyStorageService::class)->url($this->path) : null;
    }

    public function shouldRefetch(): bool
    {
        if ($this->skip_refetch || $this->is_invalid) {
            return false;
        }

        // Refetch if never fetched or older than 30 days
        return ! $this->last_fetched_at ||
               $this->last_fetched_at->lt(now()->subDays(30));
    }

    public function delete(): ?bool
    {
        // Delete from Bunny when model is deleted.
        if ($this->path) {
            app(BunnyStorageService::class)->delete($this->path);
        }

        return parent::delete();
    }
}
