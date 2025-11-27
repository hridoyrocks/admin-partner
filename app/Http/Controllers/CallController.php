<?php

namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CallController extends Controller
{
    public function index(Request $request)
    {
        $query = Call::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('callerName', 'like', "%{$search}%")
                  ->orWhere('receiverName', 'like', "%{$search}%")
                  ->orWhere('callerId', 'like', "%{$search}%")
                  ->orWhere('receiverId', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->has('date_from') && $request->date_from) {
            $dateFrom = strtotime($request->date_from);
            $query->where('callTime', '>=', $dateFrom);
        }

        if ($request->has('date_to') && $request->date_to) {
            $dateTo = strtotime($request->date_to . ' 23:59:59');
            $query->where('callTime', '<=', $dateTo);
        }

        // Duration filter (minimum duration in seconds)
        if ($request->has('min_duration') && $request->min_duration) {
            $query->where('duration', '>=', $request->min_duration);
        }

        $calls = $query->orderBy('callTime', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Transform data
        $calls->getCollection()->transform(function ($call) {
            return [
                'callId' => $call->callId,
                'callerName' => $call->callerName,
                'callerId' => $call->callerId,
                'callerProfile' => $call->callerProfile,
                'receiverName' => $call->receiverName,
                'receiverId' => $call->receiverId,
                'receiverProfile' => $call->receiverProfile,
                'duration' => $call->duration,
                'formattedDuration' => $call->formatted_duration,
                'callTime' => $call->formatted_call_time,
                'timestamp' => $call->callTime,
            ];
        });

        // Get summary stats
        $totalCalls = Call::count();
        $totalDuration = Call::sum('duration');
        $avgDuration = Call::avg('duration');

        return Inertia::render('Calls/Index', [
            'calls' => $calls,
            'filters' => [
                'search' => $request->search,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'min_duration' => $request->min_duration,
            ],
            'stats' => [
                'totalCalls' => $totalCalls,
                'totalDuration' => $this->formatDuration($totalDuration),
                'avgDuration' => $this->formatDuration($avgDuration ?? 0),
            ],
        ]);
    }

    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        }
        return sprintf('%ds', $secs);
    }
}
