<?php

namespace App\Http\Controllers\Divisi;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Widget counts — hanya permit milik divisi ini
        $counts = [
            'draft'     => Permit::where('user_id', $userId)->where('status', 'Draft')->count(),
            'submitted' => Permit::where('user_id', $userId)
                                  ->whereIn('status', ['Submitted', 'Review Staff', 'Review Manager'])
                                  ->count(),
            'active'    => Permit::where('user_id', $userId)->where('status', 'Active')->count(),
            'closed'    => Permit::where('user_id', $userId)->where('status', 'Closed')->count(),
        ];

        // 5 permit terbaru milik divisi ini, urut terbaru di atas
        $permits = Permit::with('classifications')
                          ->where('user_id', $userId)
                          ->orderByDesc('created_at')
                          ->limit(5)
                          ->get();

        return view('divisi.dashboard', compact('counts', 'permits'));
    }
}
