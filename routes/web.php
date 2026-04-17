<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RefundController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login jika belum auth, kalau sudah ke dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Admin Routes (ADMIN + SUPERADMIN)
Route::middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products - approve/reject
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::patch('/products/{product}/approve', [ProductController::class, 'approve'])->name('products.approve');
    Route::patch('/products/{product}/reject', [ProductController::class, 'reject'])->name('products.reject');
    Route::patch('/products/{product}/pending', [ProductController::class, 'setPending'])->name('products.pending');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Withdrawals
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::patch('/withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::patch('/withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

    // Users (view all)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/toggle-verify', [UserController::class, 'toggleVerify'])->name('users.toggleVerify');

    // UMKM
    Route::get('/umkms', [UmkmController::class, 'index'])->name('umkms.index');
    Route::get('/umkms/{umkm}', [UmkmController::class, 'show'])->name('umkms.show');
    Route::patch('/umkms/{umkm}/platform-fee', [UmkmController::class, 'updatePlatformFee'])->name('umkms.updatePlatformFee');
    Route::patch('/umkms/{umkm}/toggle-verify', [UmkmController::class, 'toggleVerify'])->name('umkms.toggleVerify');
    Route::delete('/umkms/{umkm}', [UmkmController::class, 'destroy'])->name('umkms.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/review', [ReportController::class, 'review'])->name('reports.review');
    Route::patch('/reports/{report}/dismiss', [ReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // Refunds
    Route::get('/refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::get('/refunds/{refund}', [RefundController::class, 'show'])->name('refunds.show');
    Route::patch('/refunds/{refund}/approve', [RefundController::class, 'approve'])->name('refunds.approve');
    Route::patch('/refunds/{refund}/reject', [RefundController::class, 'reject'])->name('refunds.reject');

    // SuperAdmin only routes
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/admin-accounts', [AdminAccountController::class, 'index'])->name('admin-accounts.index');
        Route::get('/admin-accounts/create', [AdminAccountController::class, 'create'])->name('admin-accounts.create');
        Route::post('/admin-accounts', [AdminAccountController::class, 'store'])->name('admin-accounts.store');
        Route::get('/admin-accounts/{user}/edit', [AdminAccountController::class, 'edit'])->name('admin-accounts.edit');
        Route::patch('/admin-accounts/{user}', [AdminAccountController::class, 'update'])->name('admin-accounts.update');
        Route::delete('/admin-accounts/{user}', [AdminAccountController::class, 'destroy'])->name('admin-accounts.destroy');

        // Delete Users
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Profile (Standard Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
