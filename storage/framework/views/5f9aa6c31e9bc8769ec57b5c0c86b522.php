

<?php $__env->startSection('title', 'Reports & Analytics — FinPulse'); ?>
<?php $__env->startSection('page-title', 'Reports & Analytics'); ?>
<?php $__env->startSection('page-subtitle', 'Deep insights, pivot analysis & exports'); ?>

<?php $__env->startSection('header-actions'); ?>
<div class="flex gap-2">
    <button onclick="exportExcel()" class="btn-secondary text-xs !py-2 !px-3">
        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-emerald-600"></i> Excel
    </button>
    <button onclick="exportPdf()" class="btn-secondary text-xs !py-2 !px-3">
        <i data-lucide="file-text" class="w-3.5 h-3.5 text-red-500"></i> PDF
    </button>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!-- Filters -->
<div class="flex flex-wrap items-center gap-4 mb-6">
    <div class="flex items-center gap-2 bg-white rounded-xl border border-slate-100 px-4 py-2 shadow-sm">
        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
        <select id="year-select" onchange="loadAll()" class="select-field border-0 p-0 pr-6 focus:ring-0 font-semibold text-slate-700">
            <?php for($y = now()->year; $y >= now()->year - 3; $y--): ?>
                <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="flex items-center gap-2 bg-white rounded-xl border border-slate-100 px-4 py-2 shadow-sm">
        <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
        <select id="month-select" onchange="loadBudget()" class="select-field border-0 p-0 pr-6 focus:ring-0 font-semibold text-slate-700">
            <?php $__currentLoopData = ['January','February','March','April','May','June','July','August','September','October','November','December']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($i + 1); ?>" <?php echo e(now()->month == $i+1 ? 'selected' : ''); ?>><?php echo e($m); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="pivot-table">
                <thead id="pivot-head"></thead>
                <tbody id="pivot-body"></tbody>
            </table>
        </div>
    </div>
    <div class="panel">
        <div class="panel-header">
            <div>
                <h3 class="font-semibold text-slate-900">Budget vs Actual Detail</h3>
                <p class="text-xs text-slate-400 mt-0.5">Selected month comparison</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-6 py-3">Category</th>
                        <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Budget</th>
                        <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Actual</th>
                        <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Variance</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3 w-44">Usage</th>
                        <th class="text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody id="budget-body"></tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
        document.getElementById('pivot-head').innerHTML = `
            <tr class="border-b border-slate-100 bg-slate-50">
                <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-6 py-3 sticky left-0 bg-slate-50">Category</th>
                ${months.map(m => `<th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-3 py-3 whitespace-nowrap">${m}</th>`).join('')}
                <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-indigo-600 px-4 py-3">Total</th>
            </tr>`;

        document.getElementById('pivot-body').innerHTML = rows.length === 0
            ? '<tr><td colspan="14" class="px-6 py-8 text-center text-slate-400">No data for this year.</td></tr>'
            : rows.map(row => `
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-3 font-semibold text-slate-800 whitespace-nowrap sticky left-0 bg-white">${row.category}</td>
                    ${months.map(m => {
                        const v = row[m] || 0;
                        const intensity = Math.min(v / 800, 1);
                        const bg = v ? `rgba(99,102,241,${intensity * 0.12})` : 'transparent';
                        return `<td class="px-3 py-3 text-right text-xs whitespace-nowrap" style="background:${bg}">${v ? fmt(v) : '<span class="text-slate-300">—</span>'}</td>`;
                    }).join('')}
                    <td class="px-4 py-3 text-right font-bold text-indigo-600 whitespace-nowrap">${fmt(row.total)}</td>
                </tr>
            `).join('');
    })
    .catch(err => {
        console.error(err);
        document.getElementById('pivot-body').innerHTML = '<tr><td colspan="14" class="px-6 py-8 text-center text-red-500">Failed to load pivot data.</td></tr>';
    });
}

function renderBudgetTable(data) {
    document.getElementById('budget-body').innerHTML = data.length === 0
        ? '<tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">No budget data for this month.</td></tr>'
        : data.map(row => {
            const pct = row.budget > 0 ? Math.min((row.actual / row.budget) * 100, 150) : 0;
            const color = pct > 100 ? '#ef4444' : pct > 80 ? '#f59e0b' : '#10b981';
            const over = row.difference < 0;
            return `
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-3.5 font-semibold text-slate-800">${row.category}</td>
                    <td class="px-4 py-3.5 text-right text-slate-600">${fmt(row.budget)}</td>
                    <td class="px-4 py-3.5 text-right font-semibold text-slate-800">${fmt(row.actual)}</td>
                    <td class="px-4 py-3.5 text-right font-semibold ${over ? 'text-red-500' : 'text-emerald-600'}">${over ? '-' : '+'}${fmt(Math.abs(row.difference))}</td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div style="width:${Math.min(pct,100)}%;background:${color};height:100%;border-radius:999px"></div>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 w-10">${pct.toFixed(0)}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="badge-${row.status === 'under' ? 'under' : 'over'}">${row.status === 'under' ? 'On Track' : 'Over'}</span>
                    </td>
                </tr>`;
        }).join('');
}

function loadBudget() {
    fetch('/api/reports/budget-vs-actual?month=' + getMonth() + '&year=' + getYear(), { headers: apiHeaders })
    .then(r => r.json())
    .then(data => {
        const categories = data.map(d => d.category);
        const opts = {
            series: [
                { name: 'Budget', data: data.map(d => d.budget) },
                { name: 'Actual', data: data.map(d => d.actual) },
            ],
            chart: { type: 'bar', height: Math.max(320, data.length * 40), toolbar: { show: false }, fontFamily: 'Inter' },
            colors: ['#c7d2fe', '#6366f1'],
            plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '70%' } },
            xaxis: {
                categories: categories,
                labels: { style: { colors: '#475569', fontSize: '12px', fontWeight: 500 } },
            },
            yaxis: {
                labels: { formatter: v => fmtShort(v), style: { colors: '#94a3b8' } },
            },
            grid: { borderColor: '#f1f5f9' },
            dataLabels: { enabled: false },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
            tooltip: { y: { formatter: v => fmt(v) } },
        };
        if (budgetChart) {
            budgetChart.updateOptions(opts);
        } else {
            budgetChart = new ApexCharts(document.getElementById('budget-chart'), opts);
            budgetChart.render();
        }

        renderBudgetTable(data);
    })
    .catch(err => {
        console.error(err);
        document.getElementById('budget-body').innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-red-500">Failed to load budget data.</td></tr>';
    });
}

function loadAll() { loadTrend(); loadPivot(); loadBudget(); loadExpensePie(); }
function exportParams() {
    return 'token=' + encodeURIComponent(token) + '&year=' + encodeURIComponent(getYear());
}
function exportExcel() { window.location.href = '/reports/export-excel?' + exportParams(); }
function exportPdf()   { window.location.href = '/reports/export-pdf?' + exportParams(); }

loadAll();
lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\Financial Tracker\resources\views/reports.blade.php ENDPATH**/ ?>