<?php

namespace App\Http\Controllers;

use App\Models\AppUser;
use App\Models\Rating;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = AppUser::query();

        // Search by ID (numeric database ID)
        if ($request->has('id_search') && $request->id_search) {
            $query->where('id', $request->id_search);
        }

        // Search by name/email/phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('accountStatus', $request->status);
        }

        // Sort - for registered field, order by id desc as secondary to show latest users first
        $sortField = $request->get('sort', 'registered');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'registered') {
            // Order by registered desc, then by id desc (latest inserted first for same timestamps)
            $query->orderBy('registered', $sortDirection)
                  ->orderBy('id', 'desc');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $users = $query->paginate(20)->withQueryString();

        // Transform data
        $users->getCollection()->transform(function ($user) {
            $avgRating = Rating::where('userId', $user->uid)->avg('rating');
            return [
                'id' => $user->id,
                'uid' => $user->uid,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'profileUrl' => $user->profileUrl,
                'status' => $user->status,
                'accountStatus' => $user->accountStatus,
                'totalCalls' => $user->totalCalls,
                'totalDuration' => $this->formatDuration($user->totalDuration),
                'rating' => round($avgRating ?? 0, 1),
                'registered' => $this->formatRegisteredDate($user->registered),
            ];
        });

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $request->search,
                'id_search' => $request->id_search,
                'status' => $request->status,
                'sort' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    public function banned(Request $request)
    {
        $query = AppUser::where('accountStatus', 'BANNED');

        // Search by name/email/phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        // Sort - for registered field, order by id desc as secondary to show latest users first
        $sortField = $request->get('sort', 'registered');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'registered') {
            $query->orderBy('registered', $sortDirection)
                  ->orderBy('id', 'desc');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $users = $query->paginate(20)->withQueryString();

        // Transform data
        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'uid' => $user->uid,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'profileUrl' => $user->profileUrl,
                'accountStatus' => $user->accountStatus,
                'banReason' => $user->banReason,
                'registered' => $this->formatRegisteredDate($user->registered),
            ];
        });

        return Inertia::render('Users/Banned', [
            'users' => $users,
            'filters' => [
                'search' => $request->search,
                'sort' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    public function show($uid)
    {
        $user = AppUser::where('uid', $uid)->firstOrFail();
        $avgRating = Rating::where('userId', $uid)->avg('rating');
        $ratings = Rating::where('userId', $uid)->take(10)->get();

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'uid' => $user->uid,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'profileUrl' => $user->profileUrl,
                'bio' => $user->bio,
                'status' => $user->status,
                'accountStatus' => $user->accountStatus,
                'banReason' => $user->banReason,
                'totalCalls' => $user->totalCalls,
                'totalDuration' => $this->formatDuration($user->totalDuration),
                'points' => $user->points,
                'followers' => $user->followers,
                'rating' => round($avgRating ?? 0, 1),
                'registered' => $this->formatRegisteredDate($user->registered),
            ],
            'ratings' => $ratings,
        ]);
    }

    public function updateStatus(Request $request, $uid)
    {
        $user = AppUser::where('uid', $uid)->firstOrFail();
        $user->accountStatus = $request->status;

        // Save ban reason if status is BANNED
        if ($request->status === 'BANNED' && $request->has('banReason')) {
            $user->banReason = $request->banReason;
        } elseif ($request->status === 'ACTIVE') {
            // Clear ban reason when unbanning
            $user->banReason = null;
        }

        $user->save();

        return back()->with('success', 'User status updated successfully');
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

    private function formatRegisteredDate($timestamp)
    {
        // Check if timestamp is in milliseconds (13 digits) or seconds (10 digits)
        if ($timestamp > 9999999999) {
            $timestamp = $timestamp / 1000;
        }

        return date('M d, Y', $timestamp);
    }
}
