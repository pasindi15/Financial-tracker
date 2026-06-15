

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Reports</h1>
        <p class="text-gray-500 text-sm mt-1">Analytics, pivot tables & exports</p>
    </div>
    <div class="flex gap-3">
        <button onclick="exportExcel()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">⬇ Excel</button>
        <button onclick="exportPdf()"   class="bg-red-500   text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600">⬇ PDF</button>
    </div>
</div>

<!-- Year Selector -->
<div class="flex gap-4 mb-6">
    <select id="year-select" onchange="loadAll()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600">
        <?php for($y = now()->year; $y >= now()->year - 4; $y--): ?>
            <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
        <?php endfor; ?>
    </select>
    <select id="month-select" onchange="loadBudget()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600">
        <?php $__currentLoopData = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($i + 1); ?>" <?php echo e(now()->month == $i+1 ? 'selected' : ''); ?>><?php echo e($m); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>

<!-- Monthly Trend Chart -->
<div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm mb-6">
    <h2 class="text-base font-medium text-gray-700 mb-4">Monthly Income vs Expense</h2>
    <div id="trend-chart"></div>
</div>

<!-- Pivot Table -->
<div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm mb-6">
    <h2 class="text-base font-medium text-gray-700 mb-4">Category Pivot Table</h2>
    <div id="pivot-table"></div>
</div>

<!-- Budget vs Actual -->
<div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
    <h2 class="text-base font-medium text-gray-700 mb-4">Budget vs Actual</h2>
    <div id="budget-table"></div>
</div>

<script>
const token = localStorage.getItem('auth_token');
const fmt   = v => '$' + parseFloat(v || 0).toFixed(2);
let trendChart;

function getYear()  { return document.getElementById('year-select').value; }
function getMonth() { return document.getElementById('month-select').value; }

function loadTrend() {
    fetch('/api/reports/monthly-trend?year=' + getYear(), {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(r => r.json())
    .then(data => {
        const opts = {
            series: [
                { name: 'Income',  data: data.map(d => d.income)  },
                { name: 'Expense', data: data.map(d => d.expense) },
            ],
            chart:  { type: 'area', height: 280, toolbar: { show: false }, zoom: { enabled: false } },
            colors: ['#10B981', '#EF4444'],
            stroke: { curve: 'smooth', width: 2 },
            fill:   { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            xaxis:  { categories: data.map(d => d.month) },
            dataLabels: { enabled: false },
            legend: { position: 'top' },
            tooltip: { y: { formatter: v => fmt(v) } },
        };
        if (trendChart) {
            trendChart.updateOptions({ xaxis: opts.xaxis });
            trendChart.updateSeries(opts.series);
            return;
        }
        trendChart = new ApexCharts(document.getElementById('trend-chart'), opts);
        trendChart.render();
    });
}

function loadPivot() {
    fetch('/api/reports/pivot?year=' + getYear(), {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(r => r.json())
    .then(({ months, rows }) => {
        const columns = [
            { title: 'Category', field: 'category', frozen: true, width: 140 },
            ...months.map(m => ({
                title: m, field: m, width: 80, hozAlign: 'right',
                formatter: cell => cell.getValue() ? fmt(cell.getValue()) : '—',
            })),
            { title: 'Total', field: 'total', width: 110, hozAlign: 'right',
              formatter: cell => `<strong>${fmt(cell.getValue())}</strong>` },
        ];
        new Tabulator('#pivot-table', { data: rows, columns, layout: 'fitDataFill' });
    });
}

function loadBudget() {
    fetch('/api/reports/budget-vs-actual?month=' + getMonth() + '&year=' + getYear(), {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(r => r.json())
    .then(data => {
        new Tabulator('#budget-table', {
            data,
            layout: 'fitColumns',
            columns: [
                { title: 'Category',   field: 'category',   width: 160 },
                { title: 'Budget',     field: 'budget',     width: 120, hozAlign: 'right', formatter: cell => fmt(cell.getValue()) },
                { title: 'Actual',     field: 'actual',     width: 120, hozAlign: 'right', formatter: cell => fmt(cell.getValue()) },
                { title: 'Difference', field: 'difference', width: 120, hozAlign: 'right',
                  formatter: cell => {
                    const v = cell.getValue();
                    return `<span style="color:${v >= 0 ? '#10B981' : '#EF4444'}">${fmt(Math.abs(v))} ${v >= 0 ? '▼' : '▲'}</span>`;
                  }
                },
                { title: 'Status', field: 'status', width: 100,
                  formatter: cell => {
                    const v = cell.getValue();
                    return `<span class="badge-${v === 'under' ? 'income' : 'expense'}">${v}</span>`;
                  }
                },
            ],
        });
    });
}

function loadAll() { loadTrend(); loadPivot(); loadBudget(); }

function exportExcel() {
    window.location.href = '/api/reports/export-excel?token=' + token;
}
function exportPdf() {
    window.location.href = '/api/reports/export-pdf?token=' + token;
}

loadAll();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\Financial Tracker\resources\views/reports.blade.php ENDPATH**/ ?>