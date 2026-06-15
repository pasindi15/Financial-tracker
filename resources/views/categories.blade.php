@extends('layouts.app')

@section('title', 'Categories — FinPulse')
@section('page-title', 'Categories')
@section('page-subtitle', 'Organize income and expense categories')

@section('header-actions')
<button onclick="openModal()" class="btn-primary">
    <i data-lucide="plus" class="w-4 h-4"></i> Add Category
</button>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6" id="category-cards"></div>

<div class="panel">
    <div class="panel-header">
        <h3 class="font-semibold text-slate-900">All Categories</h3>
        <span class="text-xs text-slate-400 font-medium" id="category-count"></span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" id="categories-table">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-6 py-3 w-16"></th>
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3">Name</th>
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3 w-32">Type</th>
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3 w-32">Color</th>
                    <th class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 px-4 py-3 w-28">Actions</th>
                </tr>
            </thead>
            <tbody id="categories-body"></tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="modal-backdrop hidden">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-900">New Category</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Name</label>
                <input type="text" id="m-name" class="input-field" placeholder="e.g. Groceries">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Type</label>
                <select id="m-type" class="input-field">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="m-color" value="#6366f1" class="w-12 h-12 rounded-xl border border-slate-200 cursor-pointer">
                    <div id="color-preview" class="flex-1 h-12 rounded-xl border border-slate-200 flex items-center justify-center text-sm font-semibold" style="background: #6366f115; color: #6366f1">Preview</div>
                </div>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button onclick="saveCategory()" class="btn-primary flex-1 justify-center">Save Category</button>
            <button onclick="closeModal()" class="btn-secondary flex-1 justify-center">Cancel</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function renderCategories(data) {
    const income = data.filter(c => c.type === 'income');
    const expense = data.filter(c => c.type === 'expense');

    document.getElementById('category-cards').innerHTML = `
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center"><i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i></div>
                <div><p class="text-xs font-semibold text-slate-500 uppercase">Income</p><p class="text-2xl font-bold text-slate-900">${income.length}</p></div>
            </div>
            <div class="flex flex-wrap gap-2">${income.map(c => `<span class="text-xs font-medium px-2.5 py-1 rounded-lg" style="background:${c.color}15;color:${c.color}">${c.name}</span>`).join('')}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center"><i data-lucide="trending-down" class="w-5 h-5 text-red-500"></i></div>
                <div><p class="text-xs font-semibold text-slate-500 uppercase">Expenses</p><p class="text-2xl font-bold text-slate-900">${expense.length}</p></div>
            </div>
            <div class="flex flex-wrap gap-2">${expense.map(c => `<span class="text-xs font-medium px-2.5 py-1 rounded-lg" style="background:${c.color}15;color:${c.color}">${c.name}</span>`).join('')}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center"><i data-lucide="palette" class="w-5 h-5 text-indigo-600"></i></div>
                <div><p class="text-xs font-semibold text-slate-500 uppercase">Total</p><p class="text-2xl font-bold text-slate-900">${data.length}</p></div>
            </div>
            <p class="text-xs text-slate-400">Custom categories for tracking all financial activity</p>
        </div>
    `;

    document.getElementById('category-count').textContent = data.length + ' categories';
    document.getElementById('categories-body').innerHTML = data.map(c => `
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4">
                <div class="w-8 h-8 rounded-lg shadow-sm border border-slate-100" style="background:${c.color}"></div>
            </td>
            <td class="px-4 py-4">
                <span class="font-semibold text-slate-800">${c.name}</span>
            </td>
            <td class="px-4 py-4">
                <span class="badge-${c.type}">${c.type}</span>
            </td>
            <td class="px-4 py-4">
                <span class="font-mono text-xs text-slate-400">${c.color}</span>
            </td>
            <td class="px-4 py-4">
                <button onclick="deleteCategory(${c.id})" class="text-slate-300 hover:text-red-500 transition p-1" title="Delete">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        </tr>
    `).join('');

    lucide.createIcons();
}

fetch('/api/categories', { headers: apiHeaders })
.then(r => r.json())
.then(renderCategories)
.catch(err => {
    console.error(err);
    document.getElementById('categories-body').innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-500 text-sm">Failed to load categories.</td></tr>';
});

document.getElementById('m-color').addEventListener('input', function() {
    const p = document.getElementById('color-preview');
    p.style.background = this.value + '15';
    p.style.color = this.value;
});

function openModal() { document.getElementById('modal').classList.remove('hidden'); lucide.createIcons(); }
function closeModal() { document.getElementById('modal').classList.add('hidden'); }
function saveCategory() {
    fetch('/api/categories', {
        method: 'POST', headers: apiHeaders,
        body: JSON.stringify({
            name: document.getElementById('m-name').value,
            type: document.getElementById('m-type').value,
            color: document.getElementById('m-color').value,
        })
    }).then(() => location.reload());
}
function deleteCategory(id) {
    if (!confirm('Delete this category?')) return;
    fetch('/api/categories/' + id, { method: 'DELETE', headers: apiHeaders }).then(() => location.reload());
}
lucide.createIcons();
</script>
@endpush
@endsection
