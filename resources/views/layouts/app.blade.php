<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Financial Tracker</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tabulator CSS -->
    <link href="https://unpkg.com/tabulator-tables@5.10.1/dist/css/tabulator.min.css" rel="stylesheet">
    
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.45.0/apexcharts.min.js"></script>
    
    <style>
        .tabulator { border-radius: 0.5rem; overflow: hidden; }
        .tabulator .tabulator-header { background: #F8FAFC; }
        .tabulator-row:hover { background: #F1F5F9 !important; }
        .badge-income  { background: #D1FAE5; color: #065F46; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-expense { background: #FEE2E2; color: #991B1B; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body class="bg-slate-50">
    
    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <h1 class="text-2xl font-bold text-blue-600">💰 FinTracker</h1>
                <div class="flex gap-4">
                    <a href="/dashboard" class="@if(request()->is('dashboard')) bg-blue-50 text-blue-600 @else text-gray-600 hover:bg-gray-100 @endif flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition">📊 Dashboard</a>
                    <a href="/transactions" class="@if(request()->is('transactions')) bg-blue-50 text-blue-600 @else text-gray-600 hover:bg-gray-100 @endif flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition">💳 Transactions</a>
                    <a href="/categories" class="@if(request()->is('categories')) bg-blue-50 text-blue-600 @else text-gray-600 hover:bg-gray-100 @endif flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition">🏷️ Categories</a>
                    <a href="/budgets" class="@if(request()->is('budgets')) bg-blue-50 text-blue-600 @else text-gray-600 hover:bg-gray-100 @endif flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition">🎯 Budgets</a>
                </div>
            </div>
            <button onclick="logout()" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded text-sm font-medium">🚪 Logout</button>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </div>

    <!-- Tabulator JS -->
    <script src="https://unpkg.com/tabulator-tables@5.10.1/dist/js/tabulator.min.js"></script>

    <script>
        // Check authentication
        if (!localStorage.getItem('auth_token')) {
            window.location.href = '/login';
        }

        function logout() {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
    </script>

</body>
</html>
