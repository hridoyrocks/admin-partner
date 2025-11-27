<?php

namespace App\Http\Controllers;

use App\Models\AppUser;
use App\Models\Banner;
use App\Models\Call;
use App\Models\CallTopic;
use App\Models\Report;
use App\Models\UserRank;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Time calculations
        $now = time();
        $todayStart = strtotime('today midnight');
        $yesterdayStart = strtotime('yesterday midnight');
        $weekStart = strtotime('monday this week midnight');
        $lastWeekStart = strtotime('monday last week midnight');
        $monthStart = strtotime('first day of this month midnight');

        // User stats - single query with conditional counts
        $userStats = AppUser::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN accountStatus = 'ACTIVE' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN accountStatus = 'BANNED' THEN 1 ELSE 0 END) as banned,
            SUM(CASE WHEN status = 'ONLINE' THEN 1 ELSE 0 END) as online,
            SUM(CASE WHEN registered >= ? THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN registered >= ? AND registered < ? THEN 1 ELSE 0 END) as yesterday,
            SUM(CASE WHEN registered >= ? THEN 1 ELSE 0 END) as this_week,
            SUM(CASE WHEN registered >= ? AND registered < ? THEN 1 ELSE 0 END) as last_week,
            SUM(CASE WHEN registered >= ? THEN 1 ELSE 0 END) as this_month,
            SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) as male,
            SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) as female
        ", [$todayStart, $yesterdayStart, $todayStart, $weekStart, $lastWeekStart, $weekStart, $monthStart])
        ->first();

        // Call stats - single query with conditional counts
        $callStats = Call::selectRaw("
            COUNT(*) as total,
            SUM(duration) as total_duration,
            SUM(CASE WHEN callTime >= ? THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN callTime >= ? THEN duration ELSE 0 END) as today_duration,
            SUM(CASE WHEN callTime >= ? AND callTime < ? THEN 1 ELSE 0 END) as yesterday,
            SUM(CASE WHEN callTime >= ? THEN 1 ELSE 0 END) as week,
            SUM(CASE WHEN callTime >= ? THEN duration ELSE 0 END) as week_duration,
            SUM(CASE WHEN callTime >= ? AND callTime < ? THEN 1 ELSE 0 END) as last_week,
            SUM(CASE WHEN callTime >= ? THEN 1 ELSE 0 END) as month,
            SUM(CASE WHEN callTime >= ? THEN duration ELSE 0 END) as month_duration
        ", [$todayStart, $todayStart, $yesterdayStart, $todayStart, $weekStart, $weekStart, $lastWeekStart, $weekStart, $monthStart, $monthStart])
        ->first();

        // Average call duration
        $avgCallDuration = $callStats->total > 0 ? round($callStats->total_duration / $callStats->total) : 0;

        // Reports stats - single query
        $reportStats = Report::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
            SUM(CASE WHEN time >= ? THEN 1 ELSE 0 END) as today
        ", [$todayStart])
        ->first();

        // Content stats - simple counts
        $totalTopics = CallTopic::count();
        $bannerStats = Banner::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active
        ")->first();

        // Recent calls (last 8)
        $recentCalls = Call::orderBy('callTime', 'desc')
            ->take(8)
            ->get()
            ->map(function ($call) {
                return [
                    'id' => $call->callId,
                    'caller' => $call->callerName,
                    'callerProfile' => $call->callerProfile,
                    'receiver' => $call->receiverName,
                    'receiverProfile' => $call->receiverProfile,
                    'duration' => $call->formatted_duration,
                    'time' => $this->timeAgo($call->callTime),
                ];
            });

        // Top performers (weekly)
        $topPerformers = UserRank::orderBy('totalDurationLast7Days', 'desc')
            ->take(5)
            ->get()
            ->map(function ($rank, $index) {
                return [
                    'rank' => $index + 1,
                    'userId' => $rank->userId,
                    'name' => $rank->name,
                    'profileUrl' => $rank->profileUrl,
                    'duration' => $this->formatDuration($rank->totalDurationLast7Days),
                    'calls' => $rank->totalCallsLast7Days,
                ];
            });

        // Recent reports (last 5)
        $recentReports = Report::orderBy('time', 'desc')
            ->take(5)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'userName' => $report->userName,
                    'userProfile' => $report->userProfile,
                    'reporterName' => $report->reporterName,
                    'reason' => $report->reason,
                    'status' => $report->status,
                    'time' => $this->timeAgo($report->time),
                ];
            });

        // Calls by day (last 7 days) - single optimized query
        $sevenDaysAgo = strtotime('-6 days', strtotime('today midnight'));
        $callsByDayRaw = Call::where('callTime', '>=', $sevenDaysAgo)
            ->selectRaw("
                DATE(FROM_UNIXTIME(callTime)) as date,
                COUNT(*) as calls,
                SUM(duration) as duration
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $callsByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $dayStart = strtotime("-{$i} days", strtotime('today midnight'));
            $dateKey = date('Y-m-d', $dayStart);
            $dayData = $callsByDayRaw->get($dateKey);
            $callsByDay[] = [
                'day' => date('D', $dayStart),
                'date' => date('M d', $dayStart),
                'calls' => $dayData ? $dayData->calls : 0,
                'duration' => $dayData ? round($dayData->duration / 60) : 0,
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalUsers' => (int) $userStats->total,
                'activeUsers' => (int) $userStats->active,
                'bannedUsers' => (int) $userStats->banned,
                'onlineUsers' => (int) $userStats->online,
                'newUsersToday' => (int) $userStats->today,
                'newUsersYesterday' => (int) $userStats->yesterday,
                'newUsersThisWeek' => (int) $userStats->this_week,
                'newUsersLastWeek' => (int) $userStats->last_week,
                'newUsersThisMonth' => (int) $userStats->this_month,
                'totalCalls' => (int) $callStats->total,
                'totalDuration' => $this->formatDuration($callStats->total_duration ?? 0),
                'totalDurationSeconds' => (int) ($callStats->total_duration ?? 0),
                'todayCalls' => (int) $callStats->today,
                'todayDuration' => $this->formatDuration($callStats->today_duration ?? 0),
                'yesterdayCalls' => (int) $callStats->yesterday,
                'weekCalls' => (int) $callStats->week,
                'weekDuration' => $this->formatDuration($callStats->week_duration ?? 0),
                'lastWeekCalls' => (int) $callStats->last_week,
                'monthCalls' => (int) $callStats->month,
                'monthDuration' => $this->formatDuration($callStats->month_duration ?? 0),
                'avgCallDuration' => $this->formatDuration($avgCallDuration),
                'totalReports' => (int) $reportStats->total,
                'pendingReports' => (int) $reportStats->pending,
                'resolvedReports' => (int) $reportStats->resolved,
                'reportsToday' => (int) $reportStats->today,
                'totalTopics' => $totalTopics,
                'totalBanners' => (int) $bannerStats->total,
                'activeBanners' => (int) $bannerStats->active,
                'maleUsers' => (int) $userStats->male,
                'femaleUsers' => (int) $userStats->female,
                'otherGender' => (int) ($userStats->total - $userStats->male - $userStats->female),
            ],
            'recentCalls' => $recentCalls,
            'topPerformers' => $topPerformers,
            'recentReports' => $recentReports,
            'callsByDay' => $callsByDay,
        ]);
    }

    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . 's';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }
        return sprintf('%dm', $minutes);
    }

    private function timeAgo($timestamp)
    {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . 'm ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . 'h ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . 'd ago';
        } else {
            return date('M d', $timestamp);
        }
    }
}
