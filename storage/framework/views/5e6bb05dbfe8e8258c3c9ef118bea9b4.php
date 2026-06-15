

<?php $__env->startSection('title', 'Budgets — FinPulse'); ?>
<?php $__env->startSection('page-title', 'Budgets'); ?>
<?php $__env->startSection('page-subtitle', 'Set targets and monitor spending limits'); ?>

<?php $__env->startSection('header-actions'); ?>
<div class="flex items-center gap-3">
    <select id="budget-month" onchange="loadBudgets()" class="select-field text-sm">
        <?php $__currentLoopData = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($i + 1); ?>" <?php echo e(now()->month == $i+1 ? 'selected' : ''); ?>><?php echo e($m); ?> <?php echo e(now()->year); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!-- Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="stat-card">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Total Budget</p>
        <p class="text-2xl font-bold text-slate-900" id="total-budget">—</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Total Spent</p>
        <p class="text-2xl font-bold text-red-500" id="total-spent">—</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Remaining</p>
        <p class="text-2xl font-bold text-emerald-600" id="total-remaining">—</p>
    </div>
</div>

<!-- Budget Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-6" id="budget-cards"></div>

<!-- Detail Table -->
<div class="panel">
    <div class="panel-header">
        <h3 class="font-semibold text-slate-900">Budget Details</h3>
    </div>
    <div id="budget-table"></div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function loadBudgets() {
    const month = document.getElementById('budget-month').value;
    const year = new Date().getFullYear();

    fetch('/api/reports/budget-vs-actual?month=' + month + '&year=' + year, { headers: apiHeaders })
    .then(r => r.json())
    .then(data => {
        const totalBudget = data.reduce((s, d) => s + d.budget, 0);
        const totalSpent  = data.reduce((s, d) => s + d.actual, 0);
        const remaining   = totalBudget - totalSpent;

        document.getElementById('total-budget').textContent = fmt(totalBudget);
        document.getElementById('total-spent').textContent = fmt(totalSpent);
        document.getElementById('total-remaining').textContent = fmt(remaining);

        const colors = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444','#14b8a6'];
        document.getElementById('budget-cards').innerHTML = data.map((d, i) => {
            const pct = d.budget > 0 ? Math.min((d.actual / d.budget) * 100, 100) : 0;
            const over = d.actual > d.budget;
            const color = over ? '#ef4444' : pct > 80 ? '#f59e0b' : colors[i % colors.length];
            return `
            <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-slate-800">${d.category}</h4>
                    <span class="badge-${over ? 'over' : 'under'}">${over ? 'Over' : 'On Track'}</span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-slate-500">${fmt(d.actual)} spent</span>
                    <span class="font-semibold text-slate-700">${fmt(d.budget)}</span>
                </div>
                <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500" style="width:${pct}%;background:${color}"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2">${pct.toFixed(0)}% of budget used · ${fmt(Math.abs(d.difference))} ${d.difference >= 0 ? 'remaining' : 'over'}</p>
            </div>`;
        }).join('');

        new Tabulator('#budget-table', {
            data, layout: 'fitColumns',
            columns: [
                { title: 'Category', field: 'category', width: 160,
                  formatter: c => `<span class="font-semibold text-slate-800">${c.getValue()}</span>` },
                { title: 'Budget', field: 'budget', width: 120, hozAlign: 'right', formatter: c => fmt(c.getValue()) },
                { title: 'Spent', field: 'actual', width: 120, hozAlign: 'right',
                  formatter: c => `<span class="font-semibold">${fmt(c.getValue())}</span>` },
                { title: 'Remaining', field: 'difference', width: 130, hozAlign: 'right',
                  formatter: c => {
                    const v = c.getValue();
                    return `<span class="font-semibold ${v >= 0 ? 'text-emerald-600' : 'text-red-500'}">${fmt(Math.abs(v))}</span>`;
                  }},
                { title: 'Progress', field: 'actual', width: 200,
                  formatter: c => {
                    const row = c.getRow().getData();
                    const pct = row.budget > 0 ? Math.min((row.actual / row.budget) * 100, 100) : 0;
                    const over = row.actual > row.budget;
                    const color = over ? '#ef4444' : pct > 80 ? '#f59e0b' : '#10b981';
                    return `<div class="flex items-center gap-2"><div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden"><div style="width:${pct}%;background:${color};height:100%;border-radius:999px"></div></div><span class="text-xs font-bold text-slate-500">${pct.toFixed(0)}%</span></div>`;
                  }},
                { title: 'Status', field: 'status', width: 100, hozAlign: 'center',
                  formatter: c => `<span class="badge-${c.getValue() === 'under' ? 'under' : 'over'}">${c.getValue() === 'under' ? 'On Track' : 'Over Budget'}</span>` },
            ],
        });
    });
}

loadBudgets();
lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\Financial Tracker\resources\views/budgets.blade.php ENDPATH**/ ?>