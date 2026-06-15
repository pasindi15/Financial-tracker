<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary()
    {
        $userId = auth()->id();
        $year   = now()->year;
        $month  = now()->month;

        $income  = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $expense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');

        $monthIncome = Transaction::where('user_id', $userId)->where('type', 'income')
            ->whereYear('date', $year)->whereMonth('date', $month)->sum('amount');
        $monthExpense = Transaction::where('user_id', $userId)->where('type', 'expense')
            ->whereYear('date', $year)->whereMonth('date', $month)->sum('amount');

        $prevMonthIncome = Transaction::where('user_id', $userId)->where('type', 'income')
            ->whereYear('date', $year)->whereMonth('date', $month - 1 ?: 12)
            ->when($month === 1, fn ($q) => $q->whereYear('date', $year - 1))
            ->sum('amount');
        $prevMonthExpense = Transaction::where('user_id', $userId)->where('type', 'expense')
            ->whereYear('date', $year)->whereMonth('date', $month - 1 ?: 12)
            ->when($month === 1, fn ($q) => $q->whereYear('date', $year - 1))
            ->sum('amount');

        $monthlyTrend = collect(range(1, 12))->map(function ($m) use ($year, $userId) {
            return [
                'month'   => Carbon::create($year, $m)->format('M'),
                'income'  => Transaction::where('user_id', $userId)->where('type', 'income')
                    ->whereYear('date', $year)->whereMonth('date', $m)->sum('amount'),
                'expense' => Transaction::where('user_id', $userId)->where('type', 'expense')
                    ->whereYear('date', $year)->whereMonth('date', $m)->sum('amount'),
            ];
        });

        $categoryBreakdown = Transaction::where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->whereYear('transactions.date', $year)
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->select('categories.name', 'categories.color', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        $savingsRate = $monthIncome > 0
            ? round((($monthIncome - $monthExpense) / $monthIncome) * 100, 1)
            : 0;

        return response()->json([
            'total_income'       => $income,
            'total_expense'      => $expense,
            'balance'            => $income - $expense,
            'month_income'       => $monthIncome,
            'month_expense'      => $monthExpense,
            'month_balance'      => $monthIncome - $monthExpense,
            'income_change'      => $prevMonthIncome > 0
                ? round((($monthIncome - $prevMonthIncome) / $prevMonthIncome) * 100, 1) : 0,
            'expense_change'     => $prevMonthExpense > 0
                ? round((($monthExpense - $prevMonthExpense) / $prevMonthExpense) * 100, 1) : 0,
            'savings_rate'       => $savingsRate,
            'transaction_count'  => Transaction::where('user_id', $userId)->count(),
            'monthly_trend'      => $monthlyTrend,
            'category_breakdown' => $categoryBreakdown,
        ]);
    }
}
