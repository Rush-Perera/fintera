<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FundAccountController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::get('/system/clear-all', function () {
    $commands = [
        'optimize:clear',
        'cache:clear',
        'config:clear',
        'route:clear',
        'view:clear',
        'event:clear',
    ];

    $results = [];

    foreach ($commands as $command) {
        try {
            Artisan::call($command);
            $results[$command] = trim(Artisan::output()) ?: 'OK';
        } catch (\Throwable $exception) {
            $results[$command] = 'Failed: '.$exception->getMessage();
        }
    }

    return response()->json([
        'status' => 'completed',
        'message' => 'Cache and compiled files clear process finished.',
        'results' => $results,
    ]);
})->name('system.clear-all');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/transactions/{transaction}/payslip', [TransactionController::class, 'downloadPayslip'])->name('transactions.payslip');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');

    Route::get('/fund-accounts', [FundAccountController::class, 'index'])->name('fund-accounts.index');
    Route::post('/fund-accounts', [FundAccountController::class, 'store'])->name('fund-accounts.store');
    Route::put('/fund-accounts/{fundAccount}', [FundAccountController::class, 'update'])->name('fund-accounts.update');
    Route::delete('/fund-accounts/{fundAccount}', [FundAccountController::class, 'destroy'])->name('fund-accounts.destroy');
    Route::post('/fund-transfers', [FundAccountController::class, 'transferToPaymentMethod'])->name('fund-transfers.store');
    Route::post('/fund-transfers/between-accounts', [FundAccountController::class, 'transferBetweenAccounts'])->name('fund-transfers.between-accounts');
});
