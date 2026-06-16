<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

// Public Routes
Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    return response()->json(['token' => $user->createToken('app')->plainTextToken]);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('reports/pivot', [ReportController::class, 'pivot']);
    Route::get('reports/budget-vs-actual', [ReportController::class, 'budgetVsActual']);
    Route::get('reports/monthly-trend', [ReportController::class, 'monthlyTrend']);
});
