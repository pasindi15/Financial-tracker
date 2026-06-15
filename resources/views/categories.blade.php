@extends('layouts.app')

@section('content')

<div class="mb-8 flex items-center justify-between">
    <h2 class="text-3xl font-bold text-gray-900">Categories</h2>
    <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">+ Add Category</button>
</div>

<!-- Table -->
<div class="bg-white rounded-lg border border-slate-200 p-6">
    <div id="categories-table"></div>
</div>

<!-- Add Category Modal -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Category</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" id="m-name" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Category name">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="m-type" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <div class="flex gap-2">
                    <input type="color" id="m-color" value="#3B82F6" class="w-16 h-10 rounded border border-gray-300 cursor-pointer">
                    <span id="color-preview" class="flex items-center px-3 py-2 bg-blue-100 rounded text-sm font-medium" style="background-color: rgba(59, 130, 246, 0.2);">Preview</span>
                </div>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button onclick="saveCategory()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-medium">Save</button>
            <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 font-medium">Cancel</button>
        </div>
    </div>
</div>

<script>
const token = localStorage.getItem('auth_token');

fetch('/api/categories', { headers: { 'Authorization': 'Bearer ' + token } })
.then(r => r.json())
.then(data => {
    new Tabulator('#categories-table', {
        data,
        layout: 'fitColumns',
        columns: [
            { title: 'Color', field: 'color', width: 70,
              formatter: cell => `<div style="width: 30px; height: 30px; background-color: ${cell.getValue()}; border-radius: 0.375rem; border: 1px solid #e2e8f0;"></div>` },
            { title: 'Name', field: 'name', editor: 'input', editable: true },
            { title: 'Type', field: 'type', width: 120,
              formatter: cell => `<span class="badge-${cell.getValue()}">${cell.getValue()}</span>` },
            { title: '', width: 80, hozAlign: 'center',
              formatter: () => `<button class="text-red-600 hover:text-red-800 font-medium text-sm">Delete</button>`,
              cellClick: (e, cell) => deleteCategory(cell.getRow().getData().id) },
        ],
        cellEdited: cell => {
            const row = cell.getRow().getData();
            fetch('/api/categories/' + row.id, {
                method: 'PUT',
                headers: { 
                    'Authorization': 'Bearer ' + token, 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
                },
                body: JSON.stringify({ name: cell.getValue() })
            })
            .catch(e => console.error('Error updating category:', e));
        }
    });
})
.catch(e => console.error('Error loading categories:', e));

// Color preview
document.getElementById('m-color').addEventListener('change', function() {
    document.getElementById('color-preview').style.backgroundColor = this.value + '20';
});

function openModal() { document.getElementById('modal').classList.remove('hidden'); }
function closeModal() { document.getElementById('modal').classList.add('hidden'); }

function saveCategory() {
    fetch('/api/categories', {
        method: 'POST',
        headers: { 
            'Authorization': 'Bearer ' + token, 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
        },
        body: JSON.stringify({
            name: document.getElementById('m-name').value,
            type: document.getElementById('m-type').value,
            color: document.getElementById('m-color').value,
        })
    })
    .then(r => r.json())
    .then(() => { 
        closeModal(); 
        document.getElementById('m-name').value = '';
        document.getElementById('m-color').value = '#3B82F6';
        location.reload();
    })
    .catch(e => alert('Error saving category: ' + e.message));
}

function deleteCategory(id) {
    if (!confirm('Delete this category?')) return;
    fetch('/api/categories/' + id, {
        method: 'DELETE',
        headers: { 
            'Authorization': 'Bearer ' + token,
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
        }
    })
    .then(() => location.reload())
    .catch(e => console.error('Error deleting category:', e));
}
</script>

@endsection
