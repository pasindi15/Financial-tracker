@extends('layouts.app')

@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold text-gray-900">Transactions</h2>
        <p class="text-gray-600 mt-1">Manage all your transactions</p>
    </div>
    <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">+ Add Transaction</button>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg border border-slate-200 p-4 mb-6">
    <div class="flex gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select id="filter-type" class="border border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">All Types</option>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
            <input type="date" id="filter-from" class="border border-gray-300 rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
            <input type="date" id="filter-to" class="border border-gray-300 rounded px-3 py-2 text-sm">
        </div>
        <button onclick="applyFilters()" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 font-medium">Filter</button>
        <button onclick="clearFilters()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300 font-medium">Clear</button>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-lg border border-slate-200 p-6">
    <div id="transactions-table"></div>
</div>

<!-- Add Transaction Modal -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Transaction</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="m-category" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Category</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="m-type" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" id="m-amount" step="0.01" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="m-date" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" id="m-desc" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Optional">
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button onclick="saveTransaction()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-medium">Save</button>
            <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 font-medium">Cancel</button>
        </div>
    </div>
</div>

<script>
const token = localStorage.getItem('auth_token');
const fmt = v => '$' + parseFloat(v).toFixed(2);
let table;

// Load categories into modal dropdown
fetch('/api/categories', { headers: { 'Authorization': 'Bearer ' + token } })
.then(r => r.json())
.then(cats => {
    const sel = document.getElementById('m-category');
    cats.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        sel.appendChild(opt);
    });
})
.catch(e => console.error('Error loading categories:', e));

// Load Tabulator
function loadTable(params = {}) {
    const qs = new URLSearchParams(params).toString();
    fetch('/api/transactions?' + qs, { headers: { 'Authorization': 'Bearer ' + token } })
    .then(r => r.json())
    .then(data => {
        if (table) { 
            table.replaceData(data); 
            return; 
        }
        table = new Tabulator('#transactions-table', {
            data,
            layout: 'fitColumns',
            pagination: 'local',
            paginationSize: 15,
            columns: [
                { title: 'Date', field: 'date', width: 120, editor: 'date', editable: true },
                { title: 'Description', field: 'description', hozAlign: 'left', editor: 'input', editable: true },
                { title: 'Category', field: 'category.name', width: 140 },
                { title: 'Type', field: 'type', width: 110,
                  formatter: cell => `<span class="badge-${cell.getValue()}">${cell.getValue()}</span>` },
                { title: 'Amount', field: 'amount', width: 120, hozAlign: 'right',
                  editor: 'number', editable: true,
                  formatter: cell => fmt(cell.getValue()) },
                { title: '', width: 60, hozAlign: 'center',
                  formatter: () => `<button class="text-red-600 hover:text-red-800 font-medium text-sm">Delete</button>`,
                  cellClick: (e, cell) => deleteTransaction(cell.getRow().getData().id) },
            ],
            cellEdited: cell => {
                const row = cell.getRow().getData();
                const field = cell.getField();
                fetch('/api/transactions/' + row.id, {
                    method: 'PUT',
                    headers: { 
                        'Authorization': 'Bearer ' + token, 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
                    },
                    body: JSON.stringify({ [field]: cell.getValue() })
                })
                .catch(e => console.error('Error updating transaction:', e));
            },
        });
    })
    .catch(e => console.error('Error loading transactions:', e));
}

loadTable();

function applyFilters() {
    const params = {};
    const type = document.getElementById('filter-type').value;
    const from = document.getElementById('filter-from').value;
    const to = document.getElementById('filter-to').value;
    if (type) params.type = type;
    if (from) params.date_from = from;
    if (to) params.date_to = to;
    loadTable(params);
}

function clearFilters() {
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-from').value = '';
    document.getElementById('filter-to').value = '';
    loadTable();
}

function openModal() { document.getElementById('modal').classList.remove('hidden'); }
function closeModal() { document.getElementById('modal').classList.add('hidden'); }

function saveTransaction() {
    fetch('/api/transactions', {
        method: 'POST',
        headers: { 
            'Authorization': 'Bearer ' + token, 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
        },
        body: JSON.stringify({
            category_id: document.getElementById('m-category').value,
            type: document.getElementById('m-type').value,
            amount: document.getElementById('m-amount').value,
            date: document.getElementById('m-date').value,
            description: document.getElementById('m-desc').value,
        })
    })
    .then(r => r.json())
    .then(() => { 
        closeModal(); 
        document.getElementById('m-category').value = '';
        document.getElementById('m-amount').value = '';
        document.getElementById('m-date').value = '';
        document.getElementById('m-desc').value = '';
        loadTable(); 
    })
    .catch(e => alert('Error saving transaction: ' + e.message));
}

function deleteTransaction(id) {
    if (!confirm('Delete this transaction?')) return;
    fetch('/api/transactions/' + id, {
        method: 'DELETE',
        headers: { 
            'Authorization': 'Bearer ' + token,
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
        }
    })
    .then(() => loadTable())
    .catch(e => console.error('Error deleting transaction:', e));
}
</script>

@endsection
