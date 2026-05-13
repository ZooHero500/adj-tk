<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\FeedService;
use App\Services\UserActivityService;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    use ApiHelpers;

    public function __construct()
    {
        $this->middleware('auth:web,api');
    }

    public function selfAccountFeed(Request $request)
    {
        if ($request->user()->cannot('viewAny', [Video::class])) {
            return $this->error('Please finish setting up your account', 403);
        }

        $validated = $request->validate([
            'sort' => 'sometimes|in:Latest,Popular,Oldest',
            'limit' => 'sometimes|integer|min:1|max:20',
        ]);

        $limit = data_get($validated, 'limit', 10);
        $sort = data_get($validated, 'sort', 'Latest');
        $showPinned = $sort === 'Latest';
        app(UserActivityService::class)->markActive($request->user());

        return FeedService::getAccountFeed($request->user()->profile_id, $limit, $sort, $showPinned);
    }

    public function getForYouFeed(Request $request)
    {
        $user = $request->user();
        if ($user->cannot('viewAny', [Video::class])) {
            return $this->error('Please finish setting up your account', 403);
        }
        app(UserActivityService::class)->markActive($user);
        FeedService::enforcePaginationLimit($request);
        $hideAi = $user->hide_ai;

        // First page: fetch larger pool, score & weighted-random sort
        if (! $request->has('cursor')) {
            $feed = FeedService::getVideoFeed($user->profile_id, 50, $hideAi);
            $scored = $feed->getCollection()->map(function ($video) {
                $video->_score = $this->calcNewFeedScore($video);
                return $video;
            });

            // Weighted random: score as probability weight
            $sorted = $this->weightedShuffle($scored);
            $feed->setCollection($sorted->take(15)->values());

            return VideoResource::collection($feed);
        }

        $feed = FeedService::getVideoFeed($user->profile_id, 15, $hideAi);

        return VideoResource::collection($feed);
    }

    /**
     * Score a video for the New feed.
     * Freshness 40% + Engagement 40% + Random 20%
     */
    private function calcNewFeedScore($video): float
    {
        $ageHours = max(1, now()->diffInHours($video->created_at));
        $maxAge = 30 * 24; // 30 days

        // Freshness: quadratic decay, 0-1
        $freshness = max(0, 1 - pow($ageHours / $maxAge, 2));

        // Engagement: log-normalized, 0-1
        $likes = min($video->likes ?? 0, 500);
        $comments = min($video->comments ?? 0, 100);
        $shares = min($video->shares ?? 0, 50);
        $views = min($video->views ?? 0, 5000);

        $engagementRaw = ($likes * 0.35) + ($comments * 0.25) + ($shares * 0.25) + ($views * 0.01);
        $engagement = min(1, log($engagementRaw + 1) / log(200));

        // Random factor for diversity
        $random = mt_rand(0, 100) / 100;

        return ($freshness * 0.40) + ($engagement * 0.40) + ($random * 0.20);
    }

    /**
     * Weighted shuffle: items with higher scores are more likely to appear first.
     */
    private function weightedShuffle($collection)
    {
        return $collection->sortByDesc(function ($item) {
            // Add jitter proportional to score so high-score items float up
            // but with enough randomness to vary the order each time
            return $item->_score + (mt_rand(0, 30) / 100);
        });
    }

    public function getFollowingFeed(Request $request)
    {
        $user = $request->user();
        if ($user->cannot('viewAny', [Video::class])) {
            return $this->error('Please finish setting up your account', 403);
        }

        app(UserActivityService::class)->markActive($user);

        FeedService::enforceFollowingPaginationLimit($request);

        $me = $user->profile_id;
        $hideAi = $user->hide_ai;

        $feed = Video::query()
            ->published()
            ->where(function ($q) use ($me) {
                $q->where('videos.profile_id', $me)
                    ->orWhereExists(function ($sub) use ($me) {
                        $sub->selectRaw(1)
                            ->from('followers')
                            ->whereColumn('followers.following_id', 'videos.profile_id')
                            ->where('followers.profile_id', $me);
                    });
            })
            ->when($hideAi, function ($query, $hideAi) {
                $query->where('contains_ai', false);
            })
            ->orderBy('videos.id', 'desc')
            ->cursorPaginate(5)
            ->withQueryString();

        return VideoResource::collection($feed);
    }

    public function getAccountFeedWithCursor(Request $request, $profileId)
    {
        $request->validate([
            'id' => 'required|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:20',
        ]);

        $videoId = $request->input('id');
        $limit = $request->input('limit', 10);

        $video = Video::where('profile_id', $profileId)
            ->where('id', $videoId)
            ->published()
            ->firstOrFail();

        if ($request->user() && $request->user()->cannot('view', $video->profile)) {
            return $this->error('Cannot access this profile', 403);
        }

        $authProfileId = $request->user()?->profile_id;

        $feed = Video::select('videos.*')
            ->selectRaw('CASE WHEN video_bookmarks.id IS NOT NULL THEN 1 ELSE 0 END as is_bookmarked')
            ->leftJoin('video_bookmarks', function ($join) use ($authProfileId) {
                $join->on('video_bookmarks.video_id', '=', 'videos.id')
                    ->where('video_bookmarks.profile_id', '=', $authProfileId);
            })
            ->where('videos.profile_id', $profileId)
            ->published()
            ->where('videos.id', '<=', $videoId)
            ->orderByDesc('videos.id')
            ->cursorPaginate($limit)
            ->withQueryString();

        return VideoResource::collection($feed);
    }
}
