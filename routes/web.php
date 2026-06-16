<?php

use App\Http\Controllers\Api\ReportController;
use App\Http\Middleware\AuthenticateExport;
use Illuminate\Support\Facades\Route;

Route::get('/login', fn () => view('login'))->name('login');
Route::get('/', fn () => redirect('/dashboard'));
Route::get('/dashboard', fn () => view('dashboard'));
Route::get('/transactions', fn () => view('transactions'));
Route::get('/categories', fn () => view('categories'));
Route::get('/budgets', fn () => view('budgets'));
Route::get('/reports', fn () => view('reports'));

Route::middleware(AuthenticateExport::class)->group(function () {
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel']);
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf']);
});
