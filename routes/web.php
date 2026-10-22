<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $permits = \App\Models\Permit::with(['user', 'classifications'])
        ->whereNotIn('status', ['Draft'])
        ->forSite(request('site'))
        ->when(request()->filled('tipe') && in_array(request('tipe'), ['Internal', 'Eksternal'], true),
            fn ($q) => $q->where('tipe', request('tipe')))
        ->when(request()->filled('search'), function ($q) {
            $term = '%' . trim((string) request('search')) . '%';

            $q->where(fn ($sub) => $sub
                ->where('no_permit', 'like', $term)
                ->orWhere('nama_pekerjaan', 'like', $term)
                ->orWhere('kontraktor', 'like', $term)
                ->orWhere('lokasi', 'like', $term));
        })
        ->orderByRaw('COALESCE(submitted_at, created_at) DESC')
        ->paginate(10)
        ->withQueryString()
        ->fragment('monitoring');

    $contractorStatuses = [
        \App\Models\Contractor::STATUS_WAITING,
        \App\Models\Contractor::STATUS_REVIEW,
        \App\Models\Contractor::STATUS_FORM_SUBMITTED,
        \App\Models\Contractor::STATUS_FINAL,
    ];

    $contractors = \App\Models\Contractor::query()
        ->withCount('documents')
        ->when(request()->filled('k_site') && in_array(request('k_site'), \App\Models\HsePlan::SITES, true),
            fn ($q) => $q->where('site', request('k_site')))
        ->when(request()->filled('k_status') && in_array(request('k_status'), $contractorStatuses, true),
            fn ($q) => $q->where('status', request('k_status')))
        ->when(request()->filled('k_search'), function ($q) {
            $term = '%' . trim((string) request('k_search')) . '%';

            $q->where(fn ($sub) => $sub
                ->where('vendor', 'like', $term)
                ->orWhere('pic', 'like', $term)
                ->orWhere('nama_pekerjaan', 'like', $term)
                ->orWhere('lokasi', 'like', $term));
        })
        ->orderByRaw('COALESCE(tanggal_mulai, created_at) DESC')
        ->paginate(10, ['*'], 'kontraktor_page')
        ->withQueryString()
        ->fragment('monitoring-kontraktor');

    $classifications = \App\Models\Classification::pluck('name', 'code');

    return view('welcome', compact('permits', 'contractors', 'classifications', 'contractorStatuses'));
});

// ===== CEK STATUS PERMIT PUBLIK (tanpa login) =====
Route::get('/cek-permit', [\App\Http\Controllers\Public\PermitStatusController::class, 'create'])
    ->name('public.permits.status.form');
Route::post('/cek-permit', [\App\Http\Controllers\Public\PermitStatusController::class, 'show'])
    ->middleware('throttle:10,1')
    ->name('public.permits.status');

// ===== DATA KONTRAKTOR PUBLIK (tanpa login) =====
Route::get('/data-kontraktor', [\App\Http\Controllers\Public\ContractorController::class, 'create'])
    ->middleware('throttle:10,1');
Route::post('/data-kontraktor', [\App\Http\Controllers\Public\ContractorController::class, 'store'])
    ->middleware('throttle:10,1');
Route::get('/data-kontraktor/sukses/{contractor}', [\App\Http\Controllers\Public\ContractorController::class, 'success'])
    ->middleware('signed')
    ->name('public.contractors.success');
Route::get('/data-kontraktor/{contractor}/dokumen/{document}', [\App\Http\Controllers\Public\ContractorController::class, 'viewDocument'])
    ->middleware(['signed', 'throttle:30,1'])
    ->name('public.contractors.documents.view');

// ===== HSE PLAN PUBLIK (tanpa login) =====
Route::get('/ajukan-hse-plan', [\App\Http\Controllers\Public\HsePlanController::class, 'create'])
    ->middleware('throttle:10,1');
Route::post('/ajukan-hse-plan', [\App\Http\Controllers\Public\HsePlanController::class, 'store'])
    ->middleware('throttle:10,1');
Route::get('/ajukan-hse-plan/sukses/{hsePlan}', [\App\Http\Controllers\Public\HsePlanController::class, 'success'])
    ->middleware('signed')
    ->name('public.hse-plans.success');
Route::get('/ajukan-hse-plan/sukses/{hsePlan}/file', [\App\Http\Controllers\Public\HsePlanController::class, 'file'])
    ->middleware(['signed', 'throttle:30,1'])
    ->name('public.hse-plans.file');
Route::get('/cek-hse-plan', [\App\Http\Controllers\Public\HsePlanController::class, 'statusForm'])
    ->name('public.hse-plans.status.form');
Route::post('/cek-hse-plan', [\App\Http\Controllers\Public\HsePlanController::class, 'statusShow'])
    ->middleware('throttle:10,1')
    ->name('public.hse-plans.status');

// ===== PENGAJUAN PUBLIK (tanpa login) =====
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/ajukan-permit', [\App\Http\Controllers\Public\PermitController::class, 'create']);
    Route::post('/ajukan-permit', [\App\Http\Controllers\Public\PermitController::class, 'store']);
});
Route::get('/ajukan-permit/sukses/{permit}', [\App\Http\Controllers\Public\PermitController::class, 'success'])
    ->middleware('signed')
    ->name('public.permits.success');
Route::get('/ajukan-permit/{permit}/batalkan', [\App\Http\Controllers\Public\CancellationController::class, 'show'])
    ->middleware(['signed', 'throttle:10,1'])
    ->name('public.permits.cancel.show');
Route::post('/ajukan-permit/{permit}/batalkan', [\App\Http\Controllers\Public\CancellationController::class, 'cancel'])
    ->middleware(['signed', 'throttle:10,1'])
    ->name('public.permits.cancel');

Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    $map = [
        'superadmin'     => '/superadmin/dashboard',
        'divisi'         => '/divisi/dashboard',
        'staff'          => '/admin/dashboard',
        'manager'        => '/admin/dashboard',
    ];
    return redirect($map[Auth::user()->role] ?? '/');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===== SUPER ADMIN (role:superadmin) =====
Route::middleware(['auth', 'prevent-back-history', 'role:superadmin'])->group(function () {
    Route::get('/superadmin/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index']);
    Route::resource('/superadmin/divisions', \App\Http\Controllers\SuperAdmin\DivisionController::class)->except(['show']);
    Route::get('/superadmin/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'index']);
    Route::get('/superadmin/users/create', [\App\Http\Controllers\SuperAdmin\UserController::class, 'create']);
    Route::post('/superadmin/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'store']);
    Route::get('/superadmin/users/{id}/edit', [\App\Http\Controllers\SuperAdmin\UserController::class, 'edit']);
    Route::put('/superadmin/users/{id}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'update']);
    Route::delete('/superadmin/users/{id}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'destroy']);
    Route::patch('/superadmin/users/{id}/status', [\App\Http\Controllers\SuperAdmin\UserController::class, 'updateStatus']);
    Route::patch('/superadmin/users/{id}/reset-password', [\App\Http\Controllers\SuperAdmin\UserController::class, 'resetPassword']);
    Route::get('/superadmin/permits/{id}/pdf', [\App\Http\Controllers\SuperAdmin\PermitController::class, 'viewPdf']);
    Route::get('/superadmin/permits/{id}/pdf/download', [\App\Http\Controllers\SuperAdmin\PermitController::class, 'downloadPdf']);
});

// ===== DIVISI (role:divisi) =====
Route::middleware(['auth', 'prevent-back-history', 'role:divisi'])->group(function () {
    Route::get('/divisi/dashboard', [\App\Http\Controllers\Divisi\DashboardController::class, 'index']);
    Route::get('/divisi/cancellations', [\App\Http\Controllers\Divisi\CancellationController::class, 'index']);
    Route::get('/divisi/cancellations/{id}', [\App\Http\Controllers\Divisi\CancellationController::class, 'show']);
    Route::post('/divisi/cancellations/{id}', [\App\Http\Controllers\Divisi\CancellationController::class, 'cancel']);
    Route::get('/divisi/history', [\App\Http\Controllers\Divisi\HistoryController::class, 'index']);
    Route::get('/divisi/permits/create', [\App\Http\Controllers\Divisi\PermitController::class, 'create']);
    Route::post('/divisi/permits', [\App\Http\Controllers\Divisi\PermitController::class, 'store']);
    Route::get('/divisi/permits/{id}', [\App\Http\Controllers\Divisi\PermitShowController::class, 'show']);
    Route::get('/divisi/permits/{id}/pdf', [\App\Http\Controllers\Divisi\PermitShowController::class, 'viewPdf']);
    Route::get('/divisi/permits/{id}/pdf/download', [\App\Http\Controllers\Divisi\PermitShowController::class, 'downloadPdf']);
    Route::get('/divisi/permits/{id}/edit', [\App\Http\Controllers\Divisi\PermitController::class, 'edit']);
    Route::put('/divisi/permits/{id}', [\App\Http\Controllers\Divisi\PermitController::class, 'update']);
    Route::get('/divisi/permits/{permitId}/documents/{documentId}/download', [\App\Http\Controllers\Divisi\PermitController::class, 'downloadDocument'])->name('permits.documents.download');
});

// ===== HSE PLAN (role:manager) =====
Route::middleware(['auth', 'prevent-back-history', 'role:manager'])->group(function () {
    Route::get('/admin/hse-plans', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'index']);
    Route::get('/admin/hse-plans/history', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'history']);
    Route::get('/admin/hse-plans/{id}', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'show']);
    Route::get('/admin/hse-plans/{id}/edit', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'edit']);
    Route::put('/admin/hse-plans/{id}', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'update']);
    Route::get('/admin/hse-plans/{id}/pdf', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'viewPdf']);
    Route::get('/admin/hse-plans/{id}/pdf/download', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'downloadPdf']);
    Route::post('/admin/hse-plans/{id}/approve', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'approve']);
    Route::post('/admin/hse-plans/{id}/reject', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'reject']);
    Route::get('/admin/hse-plans/{hsePlanId}/documents/{documentId}/view', [\App\Http\Controllers\Admin\HsePlanApprovalController::class, 'viewDocument'])
        ->name('admin.hse-plans.documents.view');
});

// ===== ADMIN (role:staff, manager) =====
Route::middleware(['auth', 'prevent-back-history', 'role:staff,manager'])->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index']);
    Route::get('/admin/approvals', [\App\Http\Controllers\Admin\ApprovalController::class, 'index']);
    Route::get('/admin/approvals/{id}', [\App\Http\Controllers\Admin\ApprovalController::class, 'show']);
    Route::get('/admin/approvals/{id}/edit', [\App\Http\Controllers\Admin\ApprovalController::class, 'edit']);
    Route::put('/admin/approvals/{id}/edit', [\App\Http\Controllers\Admin\ApprovalController::class, 'updateDetail']);
    Route::put('/admin/approvals/{id}', [\App\Http\Controllers\Admin\ApprovalController::class, 'update']);
    Route::get('/admin/approvals/{permitId}/documents/{documentId}/download', [\App\Http\Controllers\Admin\ApprovalController::class, 'downloadDocument'])->name('admin.permits.documents.download');
    Route::get('/admin/history', [\App\Http\Controllers\Admin\HistoryController::class, 'index']);
    Route::get('/admin/cancellations', [\App\Http\Controllers\Admin\CancellationController::class, 'index']);
    Route::get('/admin/cancellations/{id}', [\App\Http\Controllers\Admin\CancellationController::class, 'show']);
    Route::post('/admin/cancellations/{id}', [\App\Http\Controllers\Admin\CancellationController::class, 'cancel']);
    Route::get('/admin/permits/{id}/pdf', [\App\Http\Controllers\Admin\ApprovalController::class, 'viewPdf']);
    Route::get('/admin/permits/{id}/pdf/download', [\App\Http\Controllers\Admin\ApprovalController::class, 'downloadPdf']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
