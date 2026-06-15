@extends('layouts.app')

@section('title', 'Transactions — FinPulse')
@section('page-title', 'Transactions')
@section('page-subtitle', 'Track and manage all financial activity')

@section('header-actions')
<button onclick="openModal()" class="btn-primary">
    <i data-lucide="plus" class="w-4 h-4"></i> Add Transaction
</button>
@endsection

@section('content')

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="stat-card flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center"><i data-lucide="arrow-down-left" class="w-5 h-5 text-emerald-600"></i></div>
        <div><p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Income</p><p class="text-xl font-bold text-emerald-600" id="stat-income">—</p></div>
    </div>
    <div class="stat-card flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center"><i data-lucide="arrow-up-right" class="w-5 h-5 text-red-500"></i></div>
        <div><p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Expenses</p><p class="text-xl font-bold text-red-500" id="stat-expense">—</p></div>
    </div>
    <div class="stat-card flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center"><i data-lucide="hash" class="w-5 h-5 text-indigo-600"></i></div>
        <div><p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Transactions</p><p class="text-xl font-bold text-slate-900" id="stat-count">—</p></div>
    </div>
</div>

<!-- Filters -->
<div class="panel mb-6">
    <div class="panel-body">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Type</label>
                <select id="filter-type" class="select-field w-full">
                    <option value="">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">From</label>
                <input type="date" id="filter-from" class="input-field">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">To</label>
                <input type="date" id="filter-to" class="input-field">
            </div>
            <div class="flex gap-2">
                <button onclick="applyFilters()" class="btn-primary !py-2.5"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
                <button onclick="clearFilters()" class="btn-secondary !py-2.5">Clear</button>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="panel">
    <div id="transactions-table"></div>
</div>

<!-- Modal -->
<div id="modal" class="modal-backdrop hidden">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-900">New Transaction</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                <select id="m-category" class="input-field"><option value="">Select category</option></select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Type</label>
                    <select id="m-type" class="input-field">
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Amount</label>
                    <input type="number" id="m-amount" step="0.01" class="input-field" placeholder="0.00">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Date</label>
                <input type="date" id="m-date" class="input-field">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Description</label>
                <input type="text" id="m-desc" class="input-field" placeholder="What was this for?">
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button onclick="saveTransaction()" class="btn-primary flex-1 justify-center">Save Transaction</button>
            <button onclick="closeModal()" class="btn-secondary flex-1 justify-center">Cancel</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let table;

fetch('/api/categories', { headers: apiHeaders })
.then(r => r.json())
.then(cats => {
    const sel = document.getElementById('m-category');
    cats.forEach(c => { const o = document.createElement('option'); o.value = c.id; o.textContent = c.name; sel.appendChild(o); });
});

function updateStats(data) {
    const income = data.filter(t => t.type === 'income').reduce((s,t) => s + parseFloat(t.amount), 0);
    const expense = data.filter(t => t.type === 'expense').reduce((s,t) => s + parseFloat(t.amount), 0);
    document.getElementById('stat-income').textContent = fmt(income);
    document.getElementById('stat-expense').textContent = fmt(expense);
    document.getElementById('stat-count').textContent = data.length;
}

function loadTable(params = {}) {
    const qs = new URLSearchParams(params).toString();
    fetch('/api/transactions?' + qs, { headers: apiHeaders })
    .then(r => r.json())
    .then(data => {
        updateStats(data);
        if (table) { table.replaceData(data); return; }
        table = new Tabulator('#transactions-table', {
            data, layout: 'fitColumns', pagination: 'local', paginationSize: 15,
            paginationCounter: 'rows',
            columns: [
                { title: 'Date', field: 'date', width: 120, editor: 'date', editable: true,
                  formatter: c => `<span class="text-slate-500 font-medium">${c.getValue()}</span>` },
                { title: 'Description', field: 'description', editor: 'input', editable: true,
                  formatter: c => `<span class="font-medium text-slate-800">${c.getValue() || '—'}</span>` },
                { title: 'Category', field: 'category.name', width: 150,
                  formatter: c => `<span class="inline-flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-indigo-400"></span>${c.getValue()}</span>` },
                { title: 'Type', field: 'type', width: 110,
                  formatter: c => `<span class="badge-${c.getValue()}">${c.getValue()}</span>` },
                { title: 'Amount', field: 'amount', width: 130, hozAlign: 'right', editor: 'number', editable: true,
                  formatter: c => {
                    const t = c.getRow().getData().type;
                    return `<span class="font-bold ${t === 'income' ? 'text-emerald-600' : 'text-red-500'}">${t === 'income' ? '+' : '-'}${fmt(c.getValue())}</span>`;
                  }},
                { title: '', width: 50, hozAlign: 'center',
                  formatter: () => '<button class="text-slate-300 hover:text-red-500 transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>',
                  cellClick: (e, cell) => deleteTransaction(cell.getRow().getData().id) },
            ],
            cellEdited: cell => {
                const row = cell.getRow().getData();
                fetch('/api/transactions/' + row.id, { method: 'PUT', headers: apiHeaders, body: JSON.stringify({ [cell.getField()]: cell.getValue() }) });
            },
        });
        lucide.createIcons();
    });
}

loadTable();
function applyFilters() {
    const p = {};
    const t = document.getElementById('filter-type').value;
    const f = document.getElementById('filter-from').value;
    const to = document.getElementById('filter-to').value;
    if (t) p.type = t; if (f) p.date_from = f; if (to) p.date_to = to;
    loadTable(p);
}
function clearFilters() {
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-from').value = '';
    document.getElementById('filter-to').value = '';
    loadTable();
}
function openModal() { document.getElementById('modal').classList.remove('hidden'); document.getElementById('m-date').value = new Date().toISOString().split('T')[0]; lucide.createIcons(); }
function closeModal() { document.getElementById('modal').classList.add('hidden'); }
function saveTransaction() {
    fetch('/api/transactions', {
        method: 'POST', headers: apiHeaders,
        body: JSON.stringify({
            category_id: document.getElementById('m-category').value,
            type: document.getElementById('m-type').value,
            amount: document.getElementById('m-amount').value,
            date: document.getElementById('m-date').value,
            description: document.getElementById('m-desc').value,
        })
    }).then(() => { closeModal(); loadTable(); });
}
function deleteTransaction(id) {
    if (!confirm('Delete this transaction?')) return;
    fetch('/api/transactions/' + id, { method: 'DELETE', headers: apiHeaders }).then(() => loadTable());
}
lucide.createIcons();
</script>
@endpush
@endsection
