<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HsePlan;
use App\Models\Permit;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;
        $today = now()->startOfDay();

        $counts = [
            'permit_pending' => 0,
            'permit_today' => 0,
            'hse_review' => 0,
            'hse_approved' => 0,
            'hse_rejected' => 0,
        ];
        $permits = collect();
        $hsePlans = collect();

        if ($role === 'staff') {
            $permitQuery = Permit::visibleTo(Auth::user())->where('status', 'Review Staff');

            $counts['permit_pending'] = (clone $permitQuery)->count();
            $counts['permit_today'] = (clone $permitQuery)->whereDate('updated_at', $today)->count();
            $permits = $permitQuery->with(['user', 'classifications'])
                ->orderBy('submitted_at', 'asc')
                ->take(6)
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

        return view('admin.dashboard', compact('counts', 'permits', 'hsePlans', 'role'));
    }
}
