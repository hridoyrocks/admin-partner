<?php

namespace App\Http\Controllers;

use App\Models\AppUser;
use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::query();

        // Filter by status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reporterName', 'like', "%{$search}%")
                  ->orWhere('userName', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Add user info to each report
        $reports->getCollection()->transform(function ($report) {
            $reportedUser = AppUser::where('uid', $report->userId)->first();
            $reporter = AppUser::where('uid', $report->reporterId)->first();

            $report->userDbId = $reportedUser ? $reportedUser->id : null;
            $report->userPhone = $reportedUser ? $reportedUser->phone : null;
            $report->userEmail = $reportedUser ? $reportedUser->email : null;
            $report->reporterDbId = $reporter ? $reporter->id : null;
            $report->reporterPhone = $reporter ? $reporter->phone : null;
            $report->reporterEmail = $reporter ? $reporter->email : null;

            return $report;
        });

        // Stats
        $pendingCount = Report::where('status', 'pending')->count();
        $resolvedCount = Report::where('status', 'resolved')->count();
        $dismissedCount = Report::where('status', 'dismissed')->count();

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'filters' => [
                'status' => $status,
                'search' => $request->search,
            ],
            'stats' => [
                'pending' => $pendingCount,
                'resolved' => $resolvedCount,
                'dismissed' => $dismissedCount,
            ],
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $request->validate([
            'action' => 'required|in:ban,warn,dismiss',
        ]);

        $action = $request->action;

        if ($action === 'ban') {
            // Ban the reported user
            $user = AppUser::where('uid', $report->userId)->first();
            if ($user) {
                $user->accountStatus = 'BANNED';
                $user->save();
            }
            $report->status = 'resolved';
            $report->actionTaken = 'User banned';
        } elseif ($action === 'warn') {
            $report->status = 'resolved';
            $report->actionTaken = 'Warning issued';
        } else {
            $report->status = 'dismissed';
            $report->actionTaken = 'Report dismissed';
        }

        $report->resolvedBy = auth()->user()->name;
        $report->save();

        return back()->with('success', 'Report has been processed');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'action' => 'required|in:resolve,dismiss',
        ]);

        $ids = $request->ids;
        $action = $request->action;

        Report::whereIn('id', $ids)->update([
            'status' => $action === 'resolve' ? 'resolved' : 'dismissed',
            'resolvedBy' => auth()->user()->name,
            'actionTaken' => $action === 'resolve' ? 'Bulk resolved' : 'Bulk dismissed',
        ]);

        return back()->with('success', count($ids) . ' reports processed');
    }
}
