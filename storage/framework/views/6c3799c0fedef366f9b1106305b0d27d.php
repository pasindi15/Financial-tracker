

<?php $__env->startSection('content'); ?>

<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900">Dashboard</h2>
    <p class="text-gray-600 mt-1">Your financial overview</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Total Income</h3>
        <p class="text-3xl font-bold text-green-600" id="total-income">—</p>
    </div>
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Total Expense</h3>
        <p class="text-3xl font-bold text-red-600" id="total-expense">—</p>
    </div>
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Balance</h3>
        <p class="text-3xl font-bold text-blue-600" id="balance">—</p>
    </div>
</div>

<!-- Chart -->
<div class="bg-white rounded-lg border border-slate-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Income vs Expenses</h3>
    <div id="chart-income-expense" style="height: 300px;"></div>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-lg border border-slate-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h3>
    <div id="recent-table"></div>
</div>

<script>
const token = localStorage.getItem('auth_token');
const fmt = v => '$' + parseFloat(v).toFixed(2);

// Load summary cards
fetch('/api/dashboard/summary', {
    headers: { 'Authorization': 'Bearer ' + token }
})
.then(r => r.json())
.then(d => {
    document.getElementById('total-income').textContent = fmt(d.total_income);
    document.getElementById('total-expense').textContent = fmt(d.total_expense);
    document.getElementById('balance').textContent = fmt(d.balance);

    // ApexChart
    new ApexCharts(document.getElementById('chart-income-expense'), {
        series: [
            { name: 'Income', data: [d.total_income] },
            { name: 'Expense', data: [d.total_expense] },
        ],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        colors: ['#10B981', '#EF4444'],
        xaxis: { categories: ['Total'] },
        plotOptions: { bar: { columnWidth: '30%', borderRadius: 6 } },
        dataLabels: { enabled: false },
    }).render();
})
.catch(e => {
    console.error('Error loading summary:', e);
    document.getElementById('total-income').textContent = 'N/A';
    document.getElementById('total-expense').textContent = 'N/A';
    document.getElementById('balance').textContent = 'N/A';
});

// Recent Transactions Tabulator
fetch('/api/transactions?per_page=5', {
    headers: { 'Authorization': 'Bearer ' + token }
})
.then(r => r.json())
.then(data => {
    new Tabulator('#recent-table', {
        data: data.slice(0, 10),
        layout: 'fitColumns',
        pagination: 'local',
        paginationSize: 5,
        columns: [
            { title: 'Date', field: 'date', width: 120 },
            { title: 'Description', field: 'description', hozAlign: 'left' },
            { title: 'Category', field: 'category.name', width: 140 },
            { title: 'Type', field: 'type', width: 110,
              formatter: cell => `<span class="badge-${cell.getValue()}">${cell.getValue()}</span>` },
            { title: 'Amount', field: 'amount', width: 120, hozAlign: 'right',
              formatter: cell => fmt(cell.getValue()) },
        ],
    });
})
.catch(e => console.error('Error loading transactions:', e));
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\Financial Tracker\resources\views/dashboard.blade.php ENDPATH**/ ?>