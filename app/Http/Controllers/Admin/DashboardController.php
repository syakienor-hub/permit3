<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HsePlan;
use App\Models\Permit;
use App\Models\WorkInspection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $today = now()->startOfDay();

        $counts = [
            'permit_pending' => 0,
            'permit_today' => 0,
            'permit_active' => 0,
            'inspection_pending' => 0,
            'hse_review' => 0,
            'hse_approved' => 0,
            'hse_rejected' => 0,
        ];
        $permits = collect();
        $inspections = collect();
        $hsePlans = collect();

        if ($role === 'staff') {
            $permitQuery = Permit::visibleTo($user);
            $inspectionQuery = WorkInspection::query()->visibleTo($user);

            $counts['permit_pending'] = (clone $permitQuery)->where('status', 'Review Staff')->count();
            $counts['permit_today'] = (clone $permitQuery)
                ->where('status', 'Review Staff')
                ->whereDate('updated_at', $today)
                ->count();
            $counts['permit_active'] = (clone $permitQuery)->where('status', 'Active')->count();
            $counts['inspection_pending'] = (clone $inspectionQuery)
                ->where('status', WorkInspection::STATUS_PENDING)
                ->count();
            $counts['hse_review'] = HsePlan::query()
                ->visibleTo($user)
                ->where('status', HsePlan::STATUS_REVIEW)
                ->count();

            $permits = (clone $permitQuery)
                ->where('status', 'Review Staff')
                ->with(['user', 'classifications'])
                ->orderBy('submitted_at', 'asc')
                ->take(5)
                ->get();

            $inspections = (clone $inspectionQuery)
                ->where('status', WorkInspection::STATUS_PENDING)
                ->with('reviewer')
                ->orderByRaw('COALESCE(submitted_at, created_at) ASC')
                ->take(5)
                ->get();
        } elseif ($role === 'manager') {
            $permitQuery = Permit::where('status', 'Review Manager');

            $counts['permit_pending'] = (clone $permitQuery)->count();
            $counts['permit_today'] = (clone $permitQuery)->whereDate('updated_at', $today)->count();
            $counts['hse_review'] = HsePlan::where('status', HsePlan::STATUS_REVIEW)->count();
            $counts['hse_approved'] = HsePlan::where('status', HsePlan::STATUS_APPROVED)->count();
            $counts['hse_rejected'] = HsePlan::where('status', HsePlan::STATUS_REJECTED)->count();

            $permits = $permitQuery->with(['user', 'classifications'])
                ->orderBy('updated_at', 'asc')
                ->take(6)
                ->get();

            $hsePlans = HsePlan::where('status', HsePlan::STATUS_REVIEW)
                ->orderByRaw('COALESCE(submitted_at, created_at) ASC')
                ->take(6)
                ->get();
        }

        $hour = (int) now('Asia/Jakarta')->format('H');
        $greeting = match (true) {
            $hour < 11 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };

        return view('admin.dashboard', [
            'counts' => $counts,
            'permits' => $permits,
            'inspections' => $inspections,
            'hsePlans' => $hsePlans,
            'role' => $role,
            'site' => $user->site,
            'greeting' => $greeting,
            'todayLabel' => now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y'),
        ]);
    }
}
