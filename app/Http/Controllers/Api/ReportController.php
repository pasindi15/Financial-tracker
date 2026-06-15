<?php

namespace App\Http\Controllers\Api;

use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function pivot(Request $request)
    {
        $year = $request->get('year', now()->year);
        $userId = auth()->id();

        $transactions = Transaction::with('category')
            ->where('user_id', $userId)
            ->whereYear('date', $year)
            ->get();

        $pivot = [];
        foreach ($transactions as $t) {
            $month = $t->date->format('M');
            $category = $t->category->name;
            if (!isset($pivot[$category])) {
                $pivot[$category] = [];
            }
            $pivot[$category][$month] = ($pivot[$category][$month] ?? 0) + $t->amount;
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $rows = [];
        foreach ($pivot as $category => $months_data) {
            $row = ['category' => $category];
            $total = 0;
            foreach ($months as $m) {
                $val = $months_data[$m] ?? 0;
                $row[$m] = $val;
                $total += $val;
            }
            $row['total'] = $total;
            $rows[] = $row;
        }

        return response()->json(['months' => $months, 'rows' => $rows]);
    }

    public function budgetVsActual(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $userId = auth()->id();

        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $result = $budgets->map(function ($b) use ($month, $year, $userId) {
            $actual = Transaction::where('user_id', $userId)
                ->where('category_id', $b->category_id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            return [
                'category' => $b->category->name,
                'budget' => $b->amount,
                'actual' => $actual,
                'difference' => $b->amount - $actual,
                'status' => $actual <= $b->amount ? 'under' : 'over',
            ];
        });

        return response()->json($result);
    }

    public function monthlyTrend(Request $request)
    {
        $year = $request->get('year', now()->year);
        $userId = auth()->id();

        $months = collect(range(1, 12))->map(function ($m) use ($year, $userId) {
            $income = Transaction::where('user_id', $userId)->where('type', 'income')
                ->whereYear('date', $year)->whereMonth('date', $m)->sum('amount');
            $expense = Transaction::where('user_id', $userId)->where('type', 'expense')
                ->whereYear('date', $year)->whereMonth('date', $m)->sum('amount');

            return [
                'month' => Carbon::create($year, $m)->format('M'),
                'income' => $income,
                'expense' => $expense,
            ];
        });

        return response()->json($months);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new TransactionsExport($request->only(['type', 'date_from', 'date_to'])),
            'transactions-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'income' => $transactions->where('type', 'income')->sum('amount'),
            'expense' => $transactions->where('type', 'expense')->sum('amount'),
        ];
        $summary['balance'] = $summary['income'] - $summary['expense'];

        $pdf = Pdf::loadView('exports.transactions-pdf', compact('transactions', 'summary'));

        return $pdf->download('transactions-' . now()->format('Y-m-d') . '.pdf');
    }
}
