<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Financial Tracker')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50:'#eef2ff',100:'#e0e7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca',900:'#312e81' },
                    }
                }
            }
        }
    </script>
    <link href="https://unpkg.com/tabulator-tables@5.10.1/dist/css/tabulator_modern.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.45.0/apexcharts.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-400 transition-all duration-200; }
        .sidebar-link:hover { @apply text-white bg-white/10; }
        .sidebar-link.active { @apply text-white bg-gradient-to-r from-indigo-600 to-violet-600 shadow-lg shadow-indigo-500/25; }
        .stat-card { @apply bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-shadow duration-300; }
        .panel { @apply bg-white rounded-2xl border border-slate-100 shadow-sm; }
        .panel-header { @apply px-6 py-5 border-b border-slate-100 flex items-center justify-between; }
        .panel-body { @apply p-6; }
        .btn-primary { @apply inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:from-indigo-500 hover:to-violet-500 transition-all duration-200; }
        .btn-secondary { @apply inline-flex items-center gap-2 bg-slate-100 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-200 transition-colors; }
        .btn-danger { @apply inline-flex items-center gap-2 bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors; }
        .input-field { @apply w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition; }
        .select-field { @apply border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition; }
        .badge-income  { background: #ecfdf5; color: #047857; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; }
        .badge-expense { background: #fef2f2; color: #b91c1c; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; }
        .badge-under   { background: #ecfdf5; color: #047857; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-over    { background: #fef2f2; color: #b91c1c; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .tabulator { border: none !important; border-radius: 0 0 1rem 1rem; font-family: 'Inter', sans-serif !important; }
        .tabulator .tabulator-header { background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; }
        .tabulator .tabulator-header .tabulator-col { background: #f8fafc !important; border-right: none !important; }
        .tabulator .tabulator-header .tabulator-col .tabulator-col-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        .tabulator .tabulator-row { border-bottom: 1px solid #f1f5f9 !important; }
        .tabulator .tabulator-row:hover { background: #f8fafc !important; }
        .tabulator .tabulator-row .tabulator-cell { border-right: none !important; padding: 14px 16px !important; font-size: 13px; }
        .trend-up { color: #10b981; }
        .trend-down { color: #ef4444; }
        .modal-backdrop { @apply fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4; }
        .modal-content { @apply bg-white rounded-2xl shadow-2xl w-full max-w-md p-6; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col fixed h-full z-40">
        <div class="px-6 py-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i data-lucide="wallet" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">FinPulse</h1>
                    <p class="text-[11px] text-slate-400 font-medium uppercase tracking-wider">Financial Analytics</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1">
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-3">Overview</p>
            <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-[18px] h-[18px]"></i> Dashboard
            </a>
            <a href="/reports" class="sidebar-link {{ request()->is('reports') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3" class="w-[18px] h-[18px]"></i> Reports & Analytics
            </a>

            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-3 mt-8">Management</p>
            <a href="/transactions" class="sidebar-link {{ request()->is('transactions') ? 'active' : '' }}">
                <i data-lucide="arrow-left-right" class="w-[18px] h-[18px]"></i> Transactions
            </a>
            <a href="/categories" class="sidebar-link {{ request()->is('categories') ? 'active' : '' }}">
                <i data-lucide="tags" class="w-[18px] h-[18px]"></i> Categories
            </a>
            <a href="/budgets" class="sidebar-link {{ request()->is('budgets') ? 'active' : '' }}">
                <i data-lucide="target" class="w-[18px] h-[18px]"></i> Budgets
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-white/5">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-sm font-bold">AM</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">Alex Morgan</p>
                    <p class="text-[11px] text-slate-400 truncate">test@example.com</p>
                </div>
                <button onclick="logout()" class="text-slate-400 hover:text-white transition p-1" title="Logout">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 ml-64">
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30 px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-sm text-slate-500 mt-0.5">@yield('page-subtitle', '')</p>
                </div>
                <div class="flex items-center gap-3">
                    @yield('header-actions')
                    <span class="text-xs text-slate-400 font-medium bg-slate-100 px-3 py-1.5 rounded-lg">{{ now()->format('M d, Y') }}</span>
                </div>
            </div>
        </header>

        <div class="px-8 py-6">
            @yield('content')
        </div>
    </main>
</div>

<script src="https://unpkg.com/tabulator-tables@5.10.1/dist/js/tabulator.min.js"></script>
<script>
    if (!localStorage.getItem('auth_token')) window.location.href = '/login';
    function logout() { localStorage.removeItem('auth_token'); window.location.href = '/login'; }
    const token = localStorage.getItem('auth_token');
    const fmt = v => '$' + parseFloat(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const fmtShort = v => {
        const n = parseFloat(v || 0);
        if (n >= 1000000) return '$' + (n/1000000).toFixed(1) + 'M';
        if (n >= 1000) return '$' + (n/1000).toFixed(1) + 'K';
        return fmt(n);
    };
    const apiHeaders = { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content };
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>
@stack('scripts')
</body>
</html>
