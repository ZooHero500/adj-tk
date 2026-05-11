<?php

namespace App\Console\Commands;

use App\Models\Hashtag;
use App\Models\Profile;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoHashtag;
use App\Services\VideoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportBatchCommand extends Command
{
    protected $signature = 'import:batch
        {manifest : Path to the manifest JSON file}
        {--dry-run : Show what would be imported without actually doing it}
        {--skip-upload : Skip uploading files to S3 (assume already uploaded)}';

    protected $description = 'Batch import pre-transcoded videos from a manifest JSON file';

    private int $usersCreated = 0;

    private int $videosCreated = 0;

    private int $videosSkipped = 0;

    private int $filesUploaded = 0;

    public function handle(): int
    {
        $manifestPath = $this->argument('manifest');
        $dryRun = $this->option('dry-run');
        $skipUpload = $this->option('skip-upload');

        if (! file_exists($manifestPath)) {
            $this->error("Manifest file not found: {$manifestPath}");

            return Command::FAILURE;
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (! $manifest) {
            $this->error('Invalid JSON in manifest file');

            return Command::FAILURE;
        }

        $accounts = $manifest['accounts'] ?? [];
        if (empty($accounts)) {
            $this->error('No accounts found in manifest');

            return Command::FAILURE;
        }

        $totalVideos = collect($accounts)->sum(fn ($a) => count($a['videos'] ?? []));
        $this->info("Manifest loaded: ".count($accounts)." accounts, {$totalVideos} videos");

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be made');
        }

        $bar = $this->output->createProgressBar($totalVideos);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        foreach ($accounts as $accountData) {
            $username = $accountData['username'];
            $bar->setMessage("Account: @{$username}");

            if ($dryRun) {
                $bar->advance(count($accountData['videos'] ?? []));

                continue;
            }

            // Create or find user + profile
            $profile = $this->ensureAccount($accountData);

            // Import videos for this account
            foreach ($accountData['videos'] ?? [] as $videoData) {
                try {
                    $this->importVideo($profile, $videoData, $skipUpload);
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->warn("  Failed to import video: {$e->getMessage()}");
                }
                $bar->advance();
            }
        }

        $bar->setMessage('Done!');
        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Users created', $this->usersCreated],
                ['Videos imported', $this->videosCreated],
                ['Videos skipped (duplicate)', $this->videosSkipped],
                ['Files uploaded to S3', $this->filesUploaded],
            ]
        );

        return Command::SUCCESS;
    }

    private function ensureAccount(array $data): Profile
    {
        $username = $data['username'];
        $email = $data['email'] ?? "{$username}@imported.local";
        $name = $data['name'] ?? $username;
        $bio = $data['bio'] ?? '';

        $user = User::where('username', $username)->first();

        if (! $user) {
            $user = DB::transaction(function () use ($username, $email, $name, $bio) {
                $user = new User;
                $user->name = $name;
                $user->username = $username;
                $user->email = $email;
                $user->password = Hash::make(Str::random(32));
                $user->email_verified_at = now();
                $user->is_admin = false;
                $user->status = 1;
                $user->save();

                return $user;
            });

            $this->usersCreated++;
        }

        $profile = Profile::where('user_id', $user->id)->first();

        if (! $profile) {
            $profile = Profile::create([
                'user_id' => $user->id,
                'username' => $username,
                'name' => $name,
                'bio' => $bio,
                'status' => 1,
                'local' => true,
            ]);
        }

        // Upload avatar if provided
        if (! empty($data['avatar']) && file_exists($data['avatar'])) {
            $avatarPath = 'avatars/'.Str::random(20).'.'.pathinfo($data['avatar'], PATHINFO_EXTENSION);
            Storage::disk('s3')->put($avatarPath, file_get_contents($data['avatar']), 'public');
            $profile->update(['avatar' => Storage::disk('s3')->url($avatarPath)]);
            $this->filesUploaded++;
        }

        return $profile;
    }

    private function importVideo(Profile $profile, array $data, bool $skipUpload): void
    {
        $localDir = $data['transcoded_dir'] ?? null;
        $caption = $data['caption'] ?? '';
        $tags = $data['tags'] ?? [];
        $duration = $data['duration'] ?? null;
        $isAdult = $data['is_adult'] ?? false;
        $isSensitive = $data['is_sensitive'] ?? false;

        // Check for duplicate by hash if available
        if (! empty($data['sha512_hash'])) {
            $existing = Video::where('sha512_hash', $data['sha512_hash'])->first();
            if ($existing) {
                $this->videosSkipped++;

                return;
            }
        }

        // Determine S3 paths
        $s3Base = 'videos/'.Str::random(20);
        $mp4File = null;
        $hlsUploaded = false;
        $thumbUploaded = false;

        if (! $skipUpload && $localDir && is_dir($localDir)) {
            // Upload MP4
            $mp4Local = $this->findFile($localDir, '*.720p.mp4') ?? $this->findFile($localDir, '*.mp4');
            if ($mp4Local) {
                $mp4File = $s3Base.'/'.basename($mp4Local);
                Storage::disk('s3')->put($mp4File, file_get_contents($mp4Local), 'public');
                $this->filesUploaded++;
            }

            // Upload thumbnail
            $thumbLocal = $this->findFile($localDir, '*.jpg') ?? $this->findFile($localDir, '*.jpeg') ?? $this->findFile($localDir, '*.png');
            if ($thumbLocal) {
                $thumbPath = $s3Base.'/thumb.jpg';
                Storage::disk('s3')->put($thumbPath, file_get_contents($thumbLocal), 'public');
                $thumbUploaded = true;
                $this->filesUploaded++;
            }

            // Upload HLS files
            $masterM3u8 = $localDir.'/master.m3u8';
            if (file_exists($masterM3u8)) {
                $this->uploadHlsDirectory($localDir, $s3Base.'/hls');
                $hlsUploaded = true;
            }
        } elseif ($skipUpload) {
            // Expect s3_path in the manifest
            $mp4File = $data['s3_path'] ?? null;
            $hlsUploaded = ! empty($data['has_hls']);
            $thumbUploaded = ! empty($data['has_thumb']);
        }

        if (! $mp4File) {
            throw new \RuntimeException('No MP4 file found for video');
        }

        // Create video record
        $video = Video::create([
            'profile_id' => $profile->id,
            'vid' => $mp4File,
            'vid_optimized' => $mp4File,
            'caption' => $caption,
            'status' => 2,
            'visibility' => 1,
            'duration' => $duration,
            'is_sensitive' => $isSensitive,
            'is_adult' => $isAdult ? 1 : 0,
            'is_local' => true,
            'has_processed' => true,
            'has_hls' => $hlsUploaded,
            'has_thumb' => $thumbUploaded,
            'has_audio' => true,
            'can_duet' => true,
            'can_stitch' => true,
            'can_download' => true,
            'comment_state' => 4,
            'sha512_hash' => $data['sha512_hash'] ?? null,
        ]);

        // Attach hashtags
        $this->attachHashtags($video, $tags);

        // Update profile video count
        $profile->increment('video_count');

        // Clear any cached data
        VideoService::deleteMediaData($video->id);

        $this->videosCreated++;
    }

    private function attachHashtags(Video $video, array $tags): void
    {
        foreach ($tags as $tagName) {
            $tagName = ltrim(strtolower(trim($tagName)), '#');
            if (empty($tagName)) {
                continue;
            }

            $hashtag = Hashtag::firstOrCreate(
                ['name_normalized' => $tagName],
                [
                    'name' => $tagName,
                    'can_trend' => true,
                    'can_search' => true,
                    'can_autolink' => true,
                    'is_nsfw' => false,
                    'is_banned' => false,
                    'count' => 0,
                ]
            );

            VideoHashtag::firstOrCreate([
                'video_id' => $video->id,
                'hashtag_id' => $hashtag->id,
            ]);

            $hashtag->increment('count');
        }
    }

    private function uploadHlsDirectory(string $localDir, string $s3Base): void
    {
        // Upload master.m3u8
        $masterPath = $localDir.'/master.m3u8';
        if (file_exists($masterPath)) {
            Storage::disk('s3')->put($s3Base.'/master.m3u8', file_get_contents($masterPath), 'public');
            $this->filesUploaded++;
        }

        // Upload variant directories (480p, 720p, etc.)
        foreach (['480p', '720p', '1080p'] as $variant) {
            $variantDir = $localDir.'/'.$variant;
            if (! is_dir($variantDir)) {
                continue;
            }

            foreach (glob($variantDir.'/*') as $file) {
                if (is_file($file)) {
                    $s3Key = $s3Base.'/'.$variant.'/'.basename($file);
                    Storage::disk('s3')->put($s3Key, file_get_contents($file), 'public');
                    $this->filesUploaded++;
                }
            }
        }
    }

    private function findFile(string $dir, string $pattern): ?string
    {
        $files = glob($dir.'/'.$pattern);

        return $files ? $files[0] : null;
    }
}
