<?php

use App\Http\Controllers\Web\AcquisitionController;
use App\Http\Controllers\Web\AllocationController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DepartmentController;
use App\Http\Controllers\Web\IssuanceController;
use App\Http\Controllers\Web\ItemController;
use App\Http\Controllers\Web\QrController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\RequisitionController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\AdminUserController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\AssetScanController;
use App\Http\Controllers\Web\ForecastController;
use App\Http\Controllers\Web\FacilityController;
use App\Http\Controllers\Web\ActivityProposalController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register/send-code', [AuthController::class, 'sendVerificationCode'])->name('register.send-code');
    Route::post('/register/verify-code', [AuthController::class, 'verifyCode'])->name('register.verify-code');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/home', '/dashboard');

    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create')->middleware('admin');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store')->middleware('admin');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit')->middleware('admin');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update')->middleware('admin');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy')->middleware('admin');

    Route::resource('departments', DepartmentController::class)->except(['show'])->middleware('admin');
    Route::resource('suppliers', SupplierController::class)->except(['show'])->middleware('admin');
    Route::resource('allocations', AllocationController::class)->except(['show'])->middleware('admin');
    Route::resource('acquisitions', AcquisitionController::class)->except(['show'])->middleware('admin');

    Route::get('/requisitions', [RequisitionController::class, 'index'])->name('requisitions.index');
    Route::get('/requisitions/create', [RequisitionController::class, 'create'])->name('requisitions.create');
    Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');
    Route::get('/requisitions/{requisition}', [RequisitionController::class, 'show'])->name('requisitions.show');
    Route::middleware('approver')->group(function () {
        Route::post('/requisitions/{requisition}/approve', [RequisitionController::class, 'approve'])->name('requisitions.approve');
        Route::post('/requisitions/{requisition}/reject', [RequisitionController::class, 'reject'])->name('requisitions.reject');
    });

    Route::get('/issuances', [IssuanceController::class, 'index'])->name('issuances.index')->middleware('admin');
    Route::get('/issuances/create', [IssuanceController::class, 'create'])->name('issuances.create')->middleware('admin');
    Route::post('/issuances', [IssuanceController::class, 'store'])->name('issuances.store')->middleware('admin');
    Route::post('/issuances/{issuance}/return', [IssuanceController::class, 'returnItem'])->name('issuances.return')->middleware('admin');

    Route::get('/qr-scanner', [QrController::class, 'index'])->name('qr.index')->middleware('admin');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('admin');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');



    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/reserve', [FacilityController::class, 'createReservation'])->name('facilities.reserve');
    Route::post('/facilities/reservations', [FacilityController::class, 'storeReservation'])->name('facilities.reservations.store');
    Route::post('/facilities/reservations/{reservation}/approve', [FacilityController::class, 'approve'])->name('facilities.reservations.approve');
    Route::post('/facilities/reservations/{reservation}/reject', [FacilityController::class, 'reject'])->name('facilities.reservations.reject');
    Route::resource('facilities', FacilityController::class)->except(['index','show']);

    Route::get('/activity-proposals', [ActivityProposalController::class, 'index'])->name('activity-proposals.index');
    Route::get('/activity-proposals/create', [ActivityProposalController::class, 'create'])->name('activity-proposals.create');
    Route::post('/activity-proposals', [ActivityProposalController::class, 'store'])->name('activity-proposals.store');
    Route::get('/activity-proposals/{activityProposal}', [ActivityProposalController::class, 'show'])->name('activity-proposals.show');
    Route::post('/activity-proposals/{activityProposal}/approve-adviser', [ActivityProposalController::class, 'approveAdviser'])->name('activity-proposals.approve-adviser');
    Route::post('/activity-proposals/{activityProposal}/sign-dean', [ActivityProposalController::class, 'signDean'])->name('activity-proposals.sign-dean');
    Route::post('/activity-proposals/{activityProposal}/sign-sdao', [ActivityProposalController::class, 'signSdao'])->name('activity-proposals.sign-sdao');
    Route::post('/activity-proposals/{activityProposal}/sign-facilities', [ActivityProposalController::class, 'signFacilities'])->name('activity-proposals.sign-facilities');
    Route::post('/activity-proposals/{activityProposal}/sign-academic-director', [ActivityProposalController::class, 'signAcademicDirector'])->name('activity-proposals.sign-academic-director');
    Route::post('/activity-proposals/{activityProposal}/approve-executive', [ActivityProposalController::class, 'approveExecutive'])->name('activity-proposals.approve-executive');
    Route::post('/activity-proposals/{activityProposal}/reject', [ActivityProposalController::class, 'reject'])->name('activity-proposals.reject');

    Route::get('/forecasting', [ForecastController::class, 'index'])->name('forecast.index');
    Route::post('/forecasting/usage-logs', [ForecastController::class, 'storeUsageLog'])->name('forecast.usage-logs.store');
    Route::get('/asset-scans', [AssetScanController::class, 'index'])->name('asset-scans.index');
    Route::post('/asset-scans', [AssetScanController::class, 'store'])->name('asset-scans.store');

    Route::view('/settings', 'settings.index')->name('settings.index')->middleware('admin');

    Route::middleware('admin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
