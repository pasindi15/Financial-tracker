<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function summary()
    {
        $userId = auth()->id();

        $income  = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $expense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');

        return response()->json([
            'total_income'  => $income,
            'total_expense' => $expense,
            'balance'       => $income - $expense,
        ]);
    }
}
