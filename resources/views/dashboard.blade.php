@extends('layouts.app')

@section('title', 'Dashboard — FinPulse')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Your financial overview at a glance')

@section('content')

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    <div class="stat-card relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-full -translate-y-8 translate-x-8"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Income</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center"><i data-lucide="trending-up" class="w-4 h-4 text-emerald-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-slate-900" id="total-income">—</p>
            <p class="text-xs mt-2" id="income-change"></p>
        </div>
    </div>
    <div class="stat-card relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -translate-y-8 translate-x-8"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Expenses</span>
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center"><i data-lucide="trending-down" class="w-4 h-4 text-red-500"></i></div>
            </div>
            <p class="text-2xl font-bold text-slate-900" id="total-expense">—</p>
            <p class="text-xs mt-2" id="expense-change"></p>
        </div>
    </div>
    <div class="stat-card relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -translate-y-8 translate-x-8"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Net Balance</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center"><i data-lucide="landmark" class="w-4 h-4 text-indigo-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-slate-900" id="balance">—</p>
            <p class="text-xs text-slate-400 mt-2" id="tx-count"></p>
        </div>
    </div>
    <div class="stat-card relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-violet-50 rounded-full -translate-y-8 translate-x-8"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Savings Rate</span>
                <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center"><i data-lucide="piggy-bank" class="w-4 h-4 text-violet-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-slate-900" id="savings-rate">—</p>
            <p class="text-xs text-slate-400 mt-2">This month's savings</p>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="panel xl:col-span-2">
        <div class="panel-header">
            <div>
                <h3 class="font-semibold text-slate-900">Cash Flow Analysis</h3>
                <p class="text-xs text-slate-400 mt-0.5">Monthly income vs expenses</p>
            </div>
        </div>
        <div class="panel-body"><div id="chart-cashflow" style="height:320px"></div></div>
    </div>
    <div class="panel">
        <div class="panel-header">
            <div>
                <h3 class="font-semibold text-slate-900">Expense Breakdown</h3>
                <p class="text-xs text-slate-400 mt-0.5">By category this year</p>
            </div>
        </div>
        <div class="panel-body"><div id="chart-donut" style="height:320px"></div></div>
    </div>
</div>

<!-- Monthly Summary + Recent -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="panel">
        <div class="panel-header"><h3 class="font-semibold text-slate-900">This Month</h3></div>
        <div class="panel-body space-y-5">
            <div class="flex justify-between items-center">
                <span class="text-sm text-slate-500">Income</span>
                <span class="font-bold text-emerald-600" id="month-income">—</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-slate-500">Expenses</span>
                <span class="font-bold text-red-500" id="month-expense">—</span>
            </div>
            <div class="border-t border-slate-100 pt-4 flex justify-between items-center">
                <span class="text-sm font-semibold text-slate-700">Net</span>
                <span class="font-bold text-indigo-600 text-lg" id="month-balance">—</span>
            </div>
            <div id="month-bar" class="h-2 bg-slate-100 rounded-full overflow-hidden flex">
                <div id="month-income-bar" class="h-full bg-emerald-500 rounded-l-full transition-all"></div>
                <div id="month-expense-bar" class="h-full bg-red-400 rounded-r-full transition-all"></div>
            </div>
        </div>
    </div>
    <div class="panel xl:col-span-2">
        <div class="panel-header">
            <h3 class="font-semibold text-slate-900">Recent Transactions</h3>
            <a href="/transactions" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-6 py-3">Date</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Description</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Category</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Type</th>
                        <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Amount</th>
                    </tr>
                </thead>
                <tbody id="recent-table"></tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
fetch('/api/dashboard/summary', { headers: apiHeaders })
.then(r => r.json())
.then(d => {
    document.getElementById('total-income').textContent = fmt(d.total_income);
    document.getElementById('total-expense').textContent = fmt(d.total_expense);
    document.getElementById('balance').textContent = fmt(d.balance);
    document.getElementById('savings-rate').textContent = d.savings_rate + '%';
    document.getElementById('month-income').textContent = fmt(d.month_income);
    document.getElementById('month-expense').textContent = fmt(d.month_expense);
    document.getElementById('month-balance').textContent = fmt(d.month_balance);
    document.getElementById('tx-count').textContent = d.transaction_count + ' transactions recorded';

    const ic = d.income_change;
    document.getElementById('income-change').innerHTML = ic >= 0
        ? `<span class="trend-up font-semibold">↑ ${ic}%</span> <span class="text-slate-400">vs last month</span>`
        : `<span class="trend-down font-semibold">↓ ${Math.abs(ic)}%</span> <span class="text-slate-400">vs last month</span>`;
    const ec = d.expense_change;
    document.getElementById('expense-change').innerHTML = ec <= 0
        ? `<span class="trend-up font-semibold">↓ ${Math.abs(ec)}%</span> <span class="text-slate-400">vs last month</span>`
        : `<span class="trend-down font-semibold">↑ ${ec}%</span> <span class="text-slate-400">vs last month</span>`;

    const total = d.month_income + d.month_expense;
    if (total > 0) {
        document.getElementById('month-income-bar').style.width = (d.month_income / total * 100) + '%';
        document.getElementById('month-expense-bar').style.width = (d.month_expense / total * 100) + '%';
    }

    // Cash flow chart
    new ApexCharts(document.getElementById('chart-cashflow'), {
        series: [
            { name: 'Income', data: d.monthly_trend.map(m => m.income) },
            { name: 'Expenses', data: d.monthly_trend.map(m => m.expense) },
            { name: 'Net', data: d.monthly_trend.map(m => m.income - m.expense) },
        ],
        chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Inter' },
        colors: ['#10b981', '#ef4444', '#6366f1'],
        stroke: { curve: 'smooth', width: [2, 2, 3] },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
        xaxis: { categories: d.monthly_trend.map(m => m.month), labels: { style: { colors: '#94a3b8', fontSize: '12px' } } },
        yaxis: { labels: { formatter: v => fmtShort(v), style: { colors: '#94a3b8' } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        dataLabels: { enabled: false },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
        tooltip: { y: { formatter: v => fmt(v) } },
    }).render();

    // Donut chart
    const cats = d.category_breakdown;
    new ApexCharts(document.getElementById('chart-donut'), {
        series: cats.map(c => parseFloat(c.total)),
        labels: cats.map(c => c.name),
        chart: { type: 'donut', height: 320, fontFamily: 'Inter' },
        colors: cats.map(c => c.color),
        plotOptions: { pie: { donut: { size: '72%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => fmtShort(cats.reduce((s,c) => s + parseFloat(c.total), 0)) } } } } },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontSize: '11px' },
        stroke: { width: 2, colors: ['#fff'] },
        tooltip: { y: { formatter: v => fmt(v) } },
    }).render();
});

fetch('/api/transactions?per_page=8', { headers: apiHeaders })
.then(r => r.json())
.then(data => {
    document.getElementById('recent-table').innerHTML = data.slice(0, 8).map(t => `
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="px-6 py-3 text-sm text-slate-500">${(t.date || '').substring(0,10)}</td>
            <td class="px-4 py-3 text-sm font-medium text-slate-800">${t.description || '—'}</td>
            <td class="px-4 py-3 text-sm text-slate-600">${t.category?.name || '—'}</td>
            <td class="px-4 py-3"><span class="badge-${t.type}">${t.type}</span></td>
            <td class="px-4 py-3 text-right text-sm font-semibold ${t.type === 'income' ? 'text-emerald-600' : 'text-red-500'}">${t.type === 'income' ? '+' : '-'}${fmt(t.amount)}</td>
        </tr>
    `).join('');
});
</script>
@endpush
@endsection
