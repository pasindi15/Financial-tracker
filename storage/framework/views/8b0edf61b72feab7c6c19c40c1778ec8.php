

<?php $__env->startSection('title', 'Transactions — FinPulse'); ?>
<?php $__env->startSection('page-title', 'Transactions'); ?>
<?php $__env->startSection('page-subtitle', 'Track and manage all financial activity'); ?>

<?php $__env->startSection('header-actions'); ?>
<button onclick="openModal()" class="btn-primary">
    <i data-lucide="plus" class="w-4 h-4"></i> Add Transaction
</button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

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
    <div class="panel-header">
        <h3 class="font-semibold text-slate-900">All Transactions</h3>
        <span class="text-xs text-slate-400 font-medium" id="table-info"></span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-6 py-3">Date</th>
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Description</th>
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Category</th>
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3 w-28">Type</th>
                    <th class="text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3 w-32">Amount</th>
                    <th class="text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3 w-16"></th>
                </tr>
            </thead>
            <tbody id="transactions-body"></tbody>
        </table>
    </div>
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <p class="text-xs text-slate-400" id="page-info"></p>
        <div class="flex gap-2" id="pagination"></div>
    </div>
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

<?php $__env->startPush('scripts'); ?>
<script>
let allData = [];
let currentPage = 1;
const perPage = 20;

fetch('/api/categories', { headers: apiHeaders })
.then(r => r.json())
.then(cats => {
    const sel = document.getElementById('m-category');
    cats.forEach(c => { const o = document.createElement('option'); o.value = c.id; o.textContent = c.name; sel.appendChild(o); });
});

function formatDate(d) {
    if (!d) return '—';
    return d.substring(0, 10);
}

function updateStats(data) {
    const income = data.filter(t => t.type === 'income').reduce((s,t) => s + parseFloat(t.amount), 0);
    const expense = data.filter(t => t.type === 'expense').reduce((s,t) => s + parseFloat(t.amount), 0);
    document.getElementById('stat-income').textContent = fmt(income);
    document.getElementById('stat-expense').textContent = fmt(expense);
    document.getElementById('stat-count').textContent = data.length;
}

function renderPage() {
    const total = allData.length;
    const pages = Math.max(1, Math.ceil(total / perPage));
    if (currentPage > pages) currentPage = pages;
    const start = (currentPage - 1) * perPage;
    const rows = allData.slice(start, start + perPage);

    document.getElementById('table-info').textContent = total + ' total';
    document.getElementById('page-info').textContent = total === 0
        ? 'No transactions found'
        : `Showing ${start + 1}–${Math.min(start + perPage, total)} of ${total}`;

    document.getElementById('transactions-body').innerHTML = rows.length === 0
        ? '<tr><td colspan="6" class="px-6 py-10 text-center text-slate-400 text-sm">No transactions match your filters.</td></tr>'
        : rows.map(t => `
            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="px-6 py-3.5 text-sm text-slate-500 font-medium whitespace-nowrap">${formatDate(t.date)}</td>
                <td class="px-4 py-3.5 text-sm font-medium text-slate-800">${t.description || '—'}</td>
                <td class="px-4 py-3.5 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:${t.category?.color || '#94a3b8'}"></span>
                        ${t.category?.name || '—'}
                    </span>
                </td>
                <td class="px-4 py-3.5"><span class="badge-${t.type}">${t.type}</span></td>
                <td class="px-4 py-3.5 text-right text-sm font-bold whitespace-nowrap ${t.type === 'income' ? 'text-emerald-600' : 'text-red-500'}">
                    ${t.type === 'income' ? '+' : '-'}${fmt(t.amount)}
                </td>
                <td class="px-4 py-3.5 text-center">
                    <button onclick="deleteTransaction(${t.id})" class="text-slate-300 hover:text-red-500 transition p-1" title="Delete">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </td>
            </tr>
        `).join('');

    // Pagination buttons
    let btns = '';
    if (pages > 1) {
        btns += `<button onclick="goPage(${currentPage - 1})" class="btn-secondary !py-1.5 !px-3 text-xs" ${currentPage === 1 ? 'disabled style="opacity:0.4;cursor:not-allowed"' : ''}>← Prev</button>`;
        const range = [];
        for (let i = 1; i <= pages; i++) {
            if (i === 1 || i === pages || (i >= currentPage - 2 && i <= currentPage + 2)) range.push(i);
        }
        let prev = 0;
        range.forEach(i => {
            if (prev && i - prev > 1) btns += `<span class="px-1 text-slate-400 text-xs self-center">…</span>`;
            btns += `<button onclick="goPage(${i})" class="!py-1.5 !px-3 text-xs rounded-lg font-semibold ${i === currentPage ? 'bg-indigo-600 text-white' : 'btn-secondary'}">${i}</button>`;
            prev = i;
        });
        btns += `<button onclick="goPage(${currentPage + 1})" class="btn-secondary !py-1.5 !px-3 text-xs" ${currentPage === pages ? 'disabled style="opacity:0.4;cursor:not-allowed"' : ''}>Next →</button>`;
    }
    document.getElementById('pagination').innerHTML = btns;
    lucide.createIcons();
}

function goPage(p) {
    const pages = Math.ceil(allData.length / perPage);
    if (p < 1 || p > pages) return;
    currentPage = p;
    renderPage();
}

function loadTable(params = {}) {
    const qs = new URLSearchParams(params).toString();
    fetch('/api/transactions?' + qs, { headers: apiHeaders })
    .then(r => r.json())
    .then(data => {
        allData = data;
        currentPage = 1;
        updateStats(data);
        renderPage();
    })
    .catch(err => {
        console.error(err);
        document.getElementById('transactions-body').innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-red-500 text-sm">Failed to load transactions.</td></tr>';
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\Financial Tracker\resources\views/transactions.blade.php ENDPATH**/ ?>