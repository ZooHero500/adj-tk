<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReply;
use App\Models\Follower;
use App\Models\Hashtag;
use App\Models\Profile;
use App\Models\Topic;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoHashtag;
use App\Models\VideoLike;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding mock data...');

        // 1. Create users + profiles
        $users = $this->createUsers();
        $profiles = $this->createProfiles($users);

        // 2. Create hashtags & topics
        $hashtags = $this->createHashtags();
        $topics = $this->createTopics();

        // 3. Create videos
        $videos = $this->createVideos($profiles, $hashtags);

        // 4. Create comments & replies
        $comments = $this->createComments($videos, $profiles);
        $this->createCommentReplies($comments, $profiles);

        // 5. Create social interactions
        $this->createFollowers($profiles);
        $this->createVideoLikes($videos, $profiles);
        $this->createCommentLikes($comments, $profiles);

        // 6. Seed admin settings
        $this->call(AdminSettingsSeeder::class);

        $this->command->info('Mock data seeded successfully!');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Users', count($users)],
                ['Profiles', count($profiles)],
                ['Hashtags', count($hashtags)],
                ['Topics', count($topics)],
                ['Videos', count($videos)],
                ['Comments', Comment::count()],
                ['CommentReplies', CommentReply::count()],
                ['Followers', Follower::count()],
                ['VideoLikes', VideoLike::count()],
                ['CommentLikes', CommentLike::count()],
            ]
        );
    }

    private function createUsers(): array
    {
        $usersData = [
            ['name' => 'Admin User', 'username' => 'admin', 'email' => 'admin@loops.test', 'is_admin' => true],
            ['name' => 'Alice Chen', 'username' => 'alice', 'email' => 'alice@loops.test'],
            ['name' => 'Bob Wang', 'username' => 'bobwang', 'email' => 'bob@loops.test'],
            ['name' => 'Charlie Li', 'username' => 'charlie_li', 'email' => 'charlie@loops.test'],
            ['name' => 'Diana Zhang', 'username' => 'diana.z', 'email' => 'diana@loops.test'],
            ['name' => 'Eric Liu', 'username' => 'ericliu', 'email' => 'eric@loops.test'],
            ['name' => 'Fiona Wu', 'username' => 'fionawu', 'email' => 'fiona@loops.test'],
            ['name' => 'George Xu', 'username' => 'georgex', 'email' => 'george@loops.test'],
            ['name' => 'Hannah Lin', 'username' => 'hannahlin', 'email' => 'hannah@loops.test'],
            ['name' => 'Ivan Sun', 'username' => 'ivansun', 'email' => 'ivan@loops.test'],
            ['name' => 'Julia Ma', 'username' => 'juliama', 'email' => 'julia@loops.test'],
            ['name' => 'Kevin Zhao', 'username' => 'kevinz', 'email' => 'kevin@loops.test'],
            ['name' => 'Luna Yang', 'username' => 'lunayang', 'email' => 'luna@loops.test'],
            ['name' => 'Mike Huang', 'username' => 'mikehuang', 'email' => 'mike@loops.test'],
            ['name' => 'Nina Gao', 'username' => 'ninagao', 'email' => 'nina@loops.test'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $users[] = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'is_admin' => $data['is_admin'] ?? false,
                    'status' => 1,
                ]
            );
        }

        $this->command->info('  Created '.count($users).' users');

        return $users;
    }

    private function createProfiles(array $users): array
    {
        $bios = [
            'Video creator & storyteller',
            'Exploring the world one video at a time',
            'Music lover | Coffee addict | Content creator',
            'Just here for the vibes',
            'Daily vlogs & adventures',
            'Tech enthusiast sharing cool stuff',
            'Food & travel videos',
            'Dance and fitness content',
            'Comedy sketches & funny moments',
            'Photography & cinematography',
            'Digital nomad life',
            'Gaming highlights & tutorials',
            'DIY & crafts',
            'Fitness journey',
            'Art & design inspiration',
        ];

        $profiles = [];
        foreach ($users as $i => $user) {
            $profiles[] = Profile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'username' => $user->username,
                    'name' => $user->name,
                    'bio' => $bios[$i],
                    'status' => 1,
                    'local' => true,
                ]
            );
        }

        $this->command->info('  Created '.count($profiles).' profiles');

        return $profiles;
    }

    private function createHashtags(): array
    {
        $tags = [
            'fyp', 'foryou', 'viral', 'trending', 'dance', 'music',
            'comedy', 'food', 'travel', 'fitness', 'tech', 'gaming',
            'art', 'photography', 'nature', 'fashion', 'beauty',
            'cooking', 'diy', 'tutorial', 'vlog', 'pets', 'funny',
            'motivation', 'loopslife', 'creative', 'original', 'duet',
            'challenge', 'storytime',
        ];

        $hashtags = [];
        foreach ($tags as $tag) {
            $hashtags[] = Hashtag::create([
                'name' => $tag,
                'name_normalized' => strtolower($tag),
                'can_trend' => true,
                'can_search' => true,
                'can_autolink' => true,
                'is_nsfw' => false,
                'is_banned' => false,
                'count' => rand(10, 5000),
            ]);
        }

        $this->command->info('  Created '.count($hashtags).' hashtags');

        return $hashtags;
    }

    private function createTopics(): array
    {
        $topicsData = [
            ['slug' => 'entertainment', 'name' => 'Entertainment', 'topic_rank' => 10],
            ['slug' => 'music', 'name' => 'Music', 'topic_rank' => 15],
            ['slug' => 'comedy', 'name' => 'Comedy', 'topic_rank' => 20],
            ['slug' => 'food-drink', 'name' => 'Food & Drink', 'topic_rank' => 25],
            ['slug' => 'travel', 'name' => 'Travel', 'topic_rank' => 30],
            ['slug' => 'sports-fitness', 'name' => 'Sports & Fitness', 'topic_rank' => 35],
            ['slug' => 'technology', 'name' => 'Technology', 'topic_rank' => 40],
            ['slug' => 'gaming', 'name' => 'Gaming', 'topic_rank' => 45],
            ['slug' => 'education', 'name' => 'Education', 'topic_rank' => 50],
            ['slug' => 'art-design', 'name' => 'Art & Design', 'topic_rank' => 55],
        ];

        $topics = [];
        foreach ($topicsData as $data) {
            $topics[] = Topic::create([
                'slug' => $data['slug'],
                'name' => $data['name'],
                'topic_rank' => $data['topic_rank'],
                'is_active' => true,
                'total_count' => rand(50, 2000),
            ]);
        }

        $this->command->info('  Created '.count($topics).' topics');

        return $topics;
    }

    private function createVideos(array $profiles, array $hashtags): array
    {
        $captions = [
            'Check out this amazing sunset! #nature #travel #viral',
            'New dance challenge, who is in? #dance #challenge #fyp',
            'Cooking the perfect ramen from scratch #cooking #food #tutorial',
            'This trick blew my mind #tech #tutorial #viral',
            'Morning routine vlog #vlog #motivation #loopslife',
            'My cat did the funniest thing today #pets #funny #comedy',
            'Workout transformation in 30 days #fitness #motivation #challenge',
            'Street food tour in Taipei #food #travel #vlog',
            'How I built this app in a weekend #tech #diy #creative',
            'Painting timelapse - ocean waves #art #creative #original',
            'Travel tips you need to know #travel #tutorial #fyp',
            'This song hits different at night #music #foryou #viral',
            'DIY home decor on a budget #diy #creative #tutorial',
            'Gaming highlights of the week #gaming #funny #trending',
            'Fashion haul and try-on #fashion #beauty #vlog',
            'Quick and easy breakfast ideas #cooking #food #tutorial',
            'Behind the scenes of my photoshoot #photography #creative',
            'Learning a new language in 100 days #challenge #education #motivation',
            'The best coffee shops in the city #travel #food #vlog',
            'Unboxing the latest tech gadgets #tech #trending #foryou',
            'My evening skincare routine #beauty #tutorial #loopslife',
            'Epic skateboarding tricks #sports-fitness #challenge #viral',
            'Studio session - making a beat #music #creative #original',
            'Funny moments compilation #comedy #funny #trending',
            'Nature walk meditation #nature #motivation #original',
            'Day in the life of a developer #tech #vlog #loopslife',
            'Trying viral food trends #food #challenge #funny',
            'Dance cover - latest hit #dance #music #fyp',
            'Pet grooming transformation #pets #trending #viral',
            'Minimalist room makeover #diy #art #creative',
            'Sunrise yoga on the beach #fitness #nature #motivation',
            'Speed drawing portrait #art #creative #tutorial',
            'Late night coding session #tech #vlog #motivation',
            'Exploring hidden waterfalls #travel #nature #viral',
            'Street performance gone wrong #comedy #funny #storytime',
            'Meal prep for the week #cooking #food #fitness',
            'Guitar cover of a classic #music #original #foryou',
            'First time trying surfing #travel #challenge #funny',
            'Cute puppy tricks compilation #pets #funny #viral',
            'Digital art process #art #creative #tutorial',
        ];

        $videos = [];
        foreach ($captions as $i => $caption) {
            $profile = $profiles[$i % count($profiles)];
            $video = Video::create([
                'profile_id' => $profile->id,
                'caption' => $caption,
                'vid' => 'videos/mock/video_'.($i + 1).'.mp4',
                'status' => 2, // Published
                'visibility' => 1, // Public
                'duration' => rand(5, 60),
                'size_kb' => rand(500, 50000),
                'likes' => rand(0, 500),
                'comments' => 0,
                'shares' => rand(0, 100),
                'views' => rand(10, 10000),
                'is_sensitive' => false,
                'is_local' => true,
                'can_duet' => true,
                'can_stitch' => true,
                'can_download' => true,
            ]);

            // Attach 2-4 random hashtags
            $tagCount = rand(2, 4);
            $randomTags = collect($hashtags)->random($tagCount);
            foreach ($randomTags as $tag) {
                VideoHashtag::create([
                    'video_id' => $video->id,
                    'hashtag_id' => $tag->id,
                ]);
            }

            $videos[] = $video;
        }

        // Update profile video counts
        foreach ($profiles as $profile) {
            $count = Video::where('profile_id', $profile->id)->count();
            $profile->update(['video_count' => $count]);
        }

        $this->command->info('  Created '.count($videos).' videos');

        return $videos;
    }

    private function createComments(array $videos, array $profiles): array
    {
        $commentTexts = [
            'This is incredible!',
            'Love this so much',
            'How did you do that?',
            'Need a tutorial please!',
            'Absolutely stunning',
            'This made my day',
            'Wow just wow',
            'I could watch this all day',
            'You are so talented!',
            'The best thing I have seen today',
            'This deserves more views',
            'Sharing this with everyone',
            'Goals!',
            'Pure vibes',
            'Can not stop watching',
            'This is art',
            'Teach me!',
            'So cool',
            'The energy here is unmatched',
            'Masterpiece',
        ];

        $comments = [];
        foreach ($videos as $video) {
            $commentCount = rand(1, 5);
            for ($j = 0; $j < $commentCount; $j++) {
                $profile = $profiles[array_rand($profiles)];
                $comment = Comment::create([
                    'video_id' => $video->id,
                    'profile_id' => $profile->id,
                    'caption' => $commentTexts[array_rand($commentTexts)],
                    'status' => 'active',
                    'likes' => rand(0, 50),
                    'replies' => 0,
                ]);
                $comments[] = $comment;
            }

            // Update video comment count
            $video->update(['comments' => $commentCount]);
        }

        $this->command->info('  Created '.count($comments).' comments');

        return $comments;
    }

    private function createCommentReplies(array $comments, array $profiles): void
    {
        $replyTexts = [
            'Totally agree!',
            'Right?!',
            'Same here',
            'Facts',
            'Could not have said it better',
            'Yes yes yes',
            'Exactly what I was thinking',
            'Thank you!',
            'Appreciate that',
            'For real!',
        ];

        $replyCount = 0;
        foreach ($comments as $comment) {
            if (rand(0, 2) === 0) { // ~33% of comments get replies
                $numReplies = rand(1, 3);
                for ($k = 0; $k < $numReplies; $k++) {
                    $profile = $profiles[array_rand($profiles)];
                    CommentReply::create([
                        'comment_id' => $comment->id,
                        'video_id' => $comment->video_id,
                        'profile_id' => $profile->id,
                        'caption' => $replyTexts[array_rand($replyTexts)],
                        'status' => 'active',
                        'likes' => rand(0, 20),
                    ]);
                    $replyCount++;
                }
                $comment->update(['replies' => $numReplies]);
            }
        }

        $this->command->info('  Created '.$replyCount.' comment replies');
    }

    private function createFollowers(array $profiles): void
    {
        $count = 0;
        foreach ($profiles as $profile) {
            // Each user follows 3-8 random others
            $toFollow = collect($profiles)
                ->filter(fn ($p) => $p->id !== $profile->id)
                ->random(rand(3, min(8, count($profiles) - 1)));

            foreach ($toFollow as $target) {
                Follower::create([
                    'profile_id' => $profile->id,
                    'following_id' => $target->id,
                ]);
                $count++;
            }
        }

        // Update follower/following counts
        foreach ($profiles as $profile) {
            $profile->update([
                'followers' => Follower::where('following_id', $profile->id)->count(),
                'following' => Follower::where('profile_id', $profile->id)->count(),
            ]);
        }

        $this->command->info('  Created '.$count.' follower relationships');
    }

    private function createVideoLikes(array $videos, array $profiles): void
    {
        $count = 0;
        foreach ($videos as $video) {
            // Each video gets likes from 2-6 random profiles
            $likers = collect($profiles)->random(rand(2, min(6, count($profiles))));
            foreach ($likers as $profile) {
                VideoLike::create([
                    'profile_id' => $profile->id,
                    'video_id' => $video->id,
                ]);
                $count++;
            }

            // Sync the like count
            $video->update(['likes' => VideoLike::where('video_id', $video->id)->count()]);
        }

        $this->command->info('  Created '.$count.' video likes');
    }

    private function createCommentLikes(array $comments, array $profiles): void
    {
        $count = 0;
        foreach ($comments as $comment) {
            if (rand(0, 1) === 0) { // 50% of comments get likes
                $likers = collect($profiles)->random(rand(1, 3));
                foreach ($likers as $profile) {
                    CommentLike::create([
                        'profile_id' => $profile->id,
                        'comment_id' => $comment->id,
                    ]);
                    $count++;
                }
                $comment->update(['likes' => CommentLike::where('comment_id', $comment->id)->count()]);
            }
        }

        $this->command->info('  Created '.$count.' comment likes');
    }
}
