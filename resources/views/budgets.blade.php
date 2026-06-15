@extends('layouts.app')

@section('title', 'Budgets — FinPulse')
@section('page-title', 'Budgets')
@section('page-subtitle', 'Set targets and monitor spending limits')

@section('header-actions')
<div class="flex items-center gap-3">
    <select id="budget-month" onchange="loadBudgets()" class="select-field text-sm">
        @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i => $m)
            <option value="{{ $i + 1 }}" {{ now()->month == $i+1 ? 'selected' : '' }}>{{ $m }} {{ now()->year }}</option>
        @endforeach
    </select>
</div>
@endsection

@section('content')

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
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-6 py-3">Category</th>
                    <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Budget</th>
                    <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Spent</th>
                    <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Remaining</th>
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3 w-44">Progress</th>
                    <th class="text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody id="budget-table"></tbody>
        </table>
    </div>
</div>

@push('scripts')
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

        document.getElementById('budget-table').innerHTML = data.map(d => {
            const pct = d.budget > 0 ? Math.min((d.actual / d.budget) * 100, 100) : 0;
            const over = d.actual > d.budget;
            const color = over ? '#ef4444' : pct > 80 ? '#f59e0b' : '#10b981';
            return `
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="px-6 py-3.5 font-semibold text-slate-800">${d.category}</td>
                    <td class="px-4 py-3.5 text-right text-slate-600">${fmt(d.budget)}</td>
                    <td class="px-4 py-3.5 text-right font-semibold">${fmt(d.actual)}</td>
                    <td class="px-4 py-3.5 text-right font-semibold ${d.difference >= 0 ? 'text-emerald-600' : 'text-red-500'}">${fmt(Math.abs(d.difference))}</td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div style="width:${pct}%;background:${color};height:100%;border-radius:999px"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-500">${pct.toFixed(0)}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="badge-${d.status === 'under' ? 'under' : 'over'}">${d.status === 'under' ? 'On Track' : 'Over Budget'}</span>
                    </td>
                </tr>`;
        }).join('');
    });
}

loadBudgets();
lucide.createIcons();
</script>
@endpush
@endsection
