@extends('layouts.app')

@section('title', 'Reports & Analytics — FinPulse')
@section('page-title', 'Reports & Analytics')
@section('page-subtitle', 'Deep insights, pivot analysis & exports')

@section('header-actions')
<div class="flex gap-2">
    <button onclick="exportExcel()" class="btn-secondary text-xs !py-2 !px-3">
        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-emerald-600"></i> Excel
    </button>
    <button onclick="exportPdf()" class="btn-secondary text-xs !py-2 !px-3">
        <i data-lucide="file-text" class="w-3.5 h-3.5 text-red-500"></i> PDF
    </button>
</div>
@endsection

@section('content')

<!-- Filters -->
<div class="flex flex-wrap items-center gap-4 mb-6">
    <div class="flex items-center gap-2 bg-white rounded-xl border border-slate-100 px-4 py-2 shadow-sm">
        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
        <select id="year-select" onchange="loadAll()" class="select-field border-0 p-0 pr-6 focus:ring-0 font-semibold text-slate-700">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="flex items-center gap-2 bg-white rounded-xl border border-slate-100 px-4 py-2 shadow-sm">
        <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
        <select id="month-select" onchange="loadBudget()" class="select-field border-0 p-0 pr-6 focus:ring-0 font-semibold text-slate-700">
            @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $m)
                <option value="{{ $i + 1 }}" {{ now()->month == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
            @endfor
        </select>
    </div>
</div>

<!-- KPI Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6" id="report-kpis">
    <div class="stat-card"><p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">YTD Income</p><p class="text-2xl font-bold text-emerald-600" id="kpi-income">—</p></div>
    <div class="stat-card"><p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">YTD Expenses</p><p class="text-2xl font-bold text-red-500" id="kpi-expense">—</p></div>
    <div class="stat-card"><p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Net Savings</p><p class="text-2xl font-bold text-indigo-600" id="kpi-net">—</p></div>
    <div class="stat-card"><p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Savings Rate</p><p class="text-2xl font-bold text-violet-600" id="kpi-rate">—</p></div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="panel">
        <div class="panel-header">
            <div>
                <h3 class="font-semibold text-slate-900">Monthly Cash Flow</h3>
                <p class="text-xs text-slate-400 mt-0.5">Income, expenses & net savings trend</p>
            </div>
        </div>
        <div class="panel-body"><div id="trend-chart" style="height:300px"></div></div>
    </div>
    <div class="panel">
        <div class="panel-header">
            <div>
                <h3 class="font-semibold text-slate-900">Expense Distribution</h3>
                <p class="text-xs text-slate-400 mt-0.5">Category share of total spending</p>
            </div>
        </div>
        <div class="panel-body"><div id="expense-pie" style="height:300px"></div></div>
    </div>
</div>

<!-- Budget vs Actual Chart -->
<div class="panel mb-6">
    <div class="panel-header">
        <div>
            <h3 class="font-semibold text-slate-900">Budget Performance</h3>
            <p class="text-xs text-slate-400 mt-0.5">Budget vs actual spending by category</p>
        </div>
    </div>
    <div class="panel-body"><div id="budget-chart" style="height:320px"></div></div>
</div>

<!-- Tables -->
<div class="grid grid-cols-1 gap-6">
    <div class="panel">
        <div class="panel-header">
            <div>
                <h3 class="font-semibold text-slate-900">Category Pivot Table</h3>
                <p class="text-xs text-slate-400 mt-0.5">Monthly breakdown by category</p>
            </div>
        </div>
        <div id="pivot-table"></div>
    </div>
    <div class="panel">
        <div class="panel-header">
            <div>
                <h3 class="font-semibold text-slate-900">Budget vs Actual Detail</h3>
                <p class="text-xs text-slate-400 mt-0.5">Selected month comparison</p>
            </div>
        </div>
        <div id="budget-table"></div>
    </div>
</div>

@push('scripts')
<script>
let trendChart, expensePie, budgetChart;

function getYear()  { return document.getElementById('year-select').value; }
function getMonth() { return document.getElementById('month-select').value; }

function loadTrend() {
    fetch('/api/reports/monthly-trend?year=' + getYear(), { headers: apiHeaders })
    .then(r => r.json())
    .then(data => {
        const income  = data.reduce((s,d) => s + d.income, 0);
        const expense = data.reduce((s,d) => s + d.expense, 0);
        const net = income - expense;
        document.getElementById('kpi-income').textContent = fmt(income);
        document.getElementById('kpi-expense').textContent = fmt(expense);
        document.getElementById('kpi-net').textContent = fmt(net);
        document.getElementById('kpi-rate').textContent = income > 0 ? ((net/income)*100).toFixed(1) + '%' : '0%';

        const netData = data.map(d => d.income - d.expense);
        const opts = {
            series: [
                { name: 'Income',  data: data.map(d => d.income) },
                { name: 'Expense', data: data.map(d => d.expense) },
                { name: 'Net Savings', data: netData, type: 'column' },
            ],
            chart: { type: 'line', height: 300, toolbar: { show: false }, fontFamily: 'Inter' },
            colors: ['#10b981', '#ef4444', '#6366f1'],
            stroke: { curve: 'smooth', width: [3, 3, 0] },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            plotOptions: { bar: { columnWidth: '40%', borderRadius: 4 } },
            xaxis: { categories: data.map(d => d.month), labels: { style: { colors: '#94a3b8' } } },
            yaxis: { labels: { formatter: v => fmtShort(v), style: { colors: '#94a3b8' } } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
            tooltip: { y: { formatter: v => fmt(v) } },
        };
        if (trendChart) { trendChart.updateOptions(opts); return; }
        trendChart = new ApexCharts(document.getElementById('trend-chart'), opts);
        trendChart.render();
    });
}

function loadExpensePie() {
    fetch('/api/dashboard/summary', { headers: apiHeaders })
    .then(r => r.json())
    .then(d => {
        const cats = d.category_breakdown;
        const opts = {
            series: cats.map(c => parseFloat(c.total)),
            labels: cats.map(c => c.name),
            chart: { type: 'polarArea', height: 300, fontFamily: 'Inter' },
            colors: cats.map(c => c.color),
            stroke: { width: 1, colors: ['#fff'] },
            fill: { opacity: 0.85 },
            legend: { position: 'bottom', fontSize: '11px' },
            tooltip: { y: { formatter: v => fmt(v) } },
            yaxis: { show: false },
        };
        if (expensePie) { expensePie.updateOptions(opts); expensePie.updateSeries(opts.series); return; }
        expensePie = new ApexCharts(document.getElementById('expense-pie'), opts);
        expensePie.render();
    });
}

function loadPivot() {
    fetch('/api/reports/pivot?year=' + getYear(), { headers: apiHeaders })
    .then(r => r.json())
    .then(({ months, rows }) => {
        const columns = [
            { title: 'Category', field: 'category', frozen: true, width: 150,
              formatter: c => `<span class="font-semibold text-slate-800">${c.getValue()}</span>` },
            ...months.map(m => ({
                title: m, field: m, width: 85, hozAlign: 'right',
                formatter: cell => {
                    const v = cell.getValue();
                    if (!v) return '<span class="text-slate-300">—</span>';
                    const intensity = Math.min(v / 800, 1);
                    const bg = `rgba(99, 102, 241, ${intensity * 0.15})`;
                    return `<span style="background:${bg};padding:2px 8px;border-radius:6px;font-size:12px">${fmt(v)}</span>`;
                },
            })),
            { title: 'Total', field: 'total', width: 110, hozAlign: 'right',
              formatter: c => `<strong class="text-indigo-600">${fmt(c.getValue())}</strong>` },
        ];
        new Tabulator('#pivot-table', { data: rows, columns, layout: 'fitDataFill' });
    });
}

function loadBudget() {
    fetch('/api/reports/budget-vs-actual?month=' + getMonth() + '&year=' + getYear(), { headers: apiHeaders })
    .then(r => r.json())
    .then(data => {
        const opts = {
            series: [
                { name: 'Budget', data: data.map(d => d.budget) },
                { name: 'Actual', data: data.map(d => d.actual) },
            ],
            chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter' },
            colors: ['#c7d2fe', '#6366f1'],
            plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '65%' } },
            xaxis: { categories: data.map(d => d.category), labels: { formatter: v => fmtShort(v), style: { colors: '#94a3b8' } } },
            yaxis: { labels: { style: { colors: '#475569', fontSize: '12px', fontWeight: 500 } } },
            grid: { borderColor: '#f1f5f9' },
            dataLabels: { enabled: false },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
            tooltip: { y: { formatter: v => fmt(v) } },
        };
        if (budgetChart) {
            budgetChart.updateOptions({ xaxis: { categories: data.map(d => d.category) } });
            budgetChart.updateSeries(opts.series);
        } else {
            budgetChart = new ApexCharts(document.getElementById('budget-chart'), opts);
            budgetChart.render();
        }

        new Tabulator('#budget-table', {
            data,
            layout: 'fitColumns',
            columns: [
                { title: 'Category', field: 'category', width: 160,
                  formatter: c => `<span class="font-semibold text-slate-800">${c.getValue()}</span>` },
                { title: 'Budget', field: 'budget', width: 120, hozAlign: 'right', formatter: c => fmt(c.getValue()) },
                { title: 'Actual', field: 'actual', width: 120, hozAlign: 'right',
                  formatter: c => `<span class="font-semibold">${fmt(c.getValue())}</span>` },
                { title: 'Variance', field: 'difference', width: 130, hozAlign: 'right',
                  formatter: c => {
                    const v = c.getValue();
                    const over = v < 0;
                    return `<span class="font-semibold ${over ? 'text-red-500' : 'text-emerald-600'}">${over ? '-' : '+'}${fmt(Math.abs(v))}</span>`;
                  }},
                { title: 'Usage', field: 'actual', width: 160,
                  formatter: c => {
                    const row = c.getRow().getData();
                    const pct = row.budget > 0 ? Math.min((row.actual / row.budget) * 100, 150) : 0;
                    const color = pct > 100 ? '#ef4444' : pct > 80 ? '#f59e0b' : '#10b981';
                    return `<div class="flex items-center gap-2"><div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden"><div style="width:${Math.min(pct,100)}%;background:${color};height:100%;border-radius:999px"></div></div><span class="text-xs font-semibold text-slate-500 w-10">${pct.toFixed(0)}%</span></div>`;
                  }},
                { title: 'Status', field: 'status', width: 90, hozAlign: 'center',
                  formatter: c => `<span class="badge-${c.getValue() === 'under' ? 'under' : 'over'}">${c.getValue() === 'under' ? 'On Track' : 'Over'}</span>` },
            ],
        });
    });
}

function loadAll() { loadTrend(); loadPivot(); loadBudget(); loadExpensePie(); }
function exportExcel() { window.location.href = '/api/reports/export-excel?token=' + token; }
function exportPdf()   { window.location.href = '/api/reports/export-pdf?token=' + token; }

loadAll();
lucide.createIcons();
</script>
@endpush
@endsection
