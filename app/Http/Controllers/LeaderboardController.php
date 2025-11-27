<?php

namespace App\Http\Controllers;

use App\Models\UserRank;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'weekly');

        $query = UserRank::query();

        // Sort based on period
        switch ($period) {
            case 'weekly':
                $query->orderBy('totalDurationLast7Days', 'desc');
                break;
            case 'monthly':
                $query->orderBy('totalDurationLast30Days', 'desc');
                break;
            case 'lifetime':
                $query->orderBy('totalLifetimeDuration', 'desc');
                break;
        }

        $ranks = $query->paginate(50)->withQueryString();

        // Transform data with rank position
        $startRank = ($ranks->currentPage() - 1) * $ranks->perPage() + 1;

        $ranks->getCollection()->transform(function ($rank, $index) use ($period, $startRank) {
            $duration = match($period) {
                'weekly' => $rank->totalDurationLast7Days,
                'monthly' => $rank->totalDurationLast30Days,
                'lifetime' => $rank->totalLifetimeDuration,
            };

            $calls = match($period) {
                'weekly' => $rank->totalCallsLast7Days,
                'monthly' => $rank->totalCallsLast30Days,
                'lifetime' => $rank->totalLifetimeCalls,
            };

            return [
                'position' => $startRank + $index,
                'userId' => $rank->userId,
                'name' => $rank->name,
                'profileUrl' => $rank->profileUrl,
                'duration' => $duration,
                'formattedDuration' => $this->formatDuration($duration),
                'calls' => $calls,
            ];
        });

        return Inertia::render('Leaderboard/Index', [
            'ranks' => $ranks,
            'period' => $period,
        ]);
    }

    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }
        return sprintf('%dm', $minutes);
    }
}
