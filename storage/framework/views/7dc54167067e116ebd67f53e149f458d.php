<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Financial Tracker'); ?></title>

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

        /* Sidebar */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: #94a3b8;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .sidebar-link:hover {
            color: #f1f5f9;
            background: rgba(255,255,255,0.07);
        }
        .sidebar-link.active {
            color: #fff;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            box-shadow: 0 4px 14px rgba(99,102,241,0.35);
        }
        .sidebar-link .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.06);
            flex-shrink: 0;
            transition: background 0.2s;
        }
        .sidebar-link:hover .nav-icon { background: rgba(255,255,255,0.1); }
        .sidebar-link.active .nav-icon { background: rgba(255,255,255,0.2); }
        .sidebar-link .nav-icon svg { width: 16px; height: 16px; }

        .stat-card { background:#fff; border-radius:1rem; border:1px solid #f1f5f9; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,0.04); transition:box-shadow 0.3s; }
        .stat-card:hover { box-shadow:0 4px 12px rgba(0,0,0,0.08); }
        .panel { background:#fff; border-radius:1rem; border:1px solid #f1f5f9; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
        .panel-header { padding:1.25rem 1.5rem; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
        .panel-body { padding:1.5rem; }
        .btn-primary { display:inline-flex; align-items:center; gap:0.5rem; background:linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; padding:0.625rem 1.25rem; border-radius:0.75rem; font-size:0.875rem; font-weight:600; box-shadow:0 4px 14px rgba(99,102,241,0.3); border:none; cursor:pointer; transition:all 0.2s; }
        .btn-primary:hover { box-shadow:0 6px 20px rgba(99,102,241,0.4); }
        .btn-secondary { display:inline-flex; align-items:center; gap:0.5rem; background:#f1f5f9; color:#334155; padding:0.625rem 1.25rem; border-radius:0.75rem; font-size:0.875rem; font-weight:600; border:none; cursor:pointer; transition:background 0.2s; }
        .btn-secondary:hover { background:#e2e8f0; }
        .input-field { width:100%; border:1px solid #e2e8f0; border-radius:0.75rem; padding:0.625rem 1rem; font-size:0.875rem; color:#334155; background:#fff; outline:none; transition:border 0.2s,box-shadow 0.2s; }
        .input-field:focus { border-color:#818cf8; box-shadow:0 0 0 3px rgba(99,102,241,0.15); }
        .select-field { border:1px solid #e2e8f0; border-radius:0.75rem; padding:0.625rem 1rem; font-size:0.875rem; color:#334155; background:#fff; outline:none; }
        .badge-income  { background:#ecfdf5; color:#047857; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.03em; }
        .badge-expense { background:#fef2f2; color:#b91c1c; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.03em; }
        .badge-under   { background:#ecfdf5; color:#047857; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:600; }
        .badge-over    { background:#fef2f2; color:#b91c1c; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:600; }
        .tabulator { border:none !important; border-radius:0 0 1rem 1rem; font-family:'Inter',sans-serif !important; }
        .tabulator .tabulator-header { background:#f8fafc !important; border-bottom:1px solid #e2e8f0 !important; }
        .tabulator .tabulator-header .tabulator-col { background:#f8fafc !important; border-right:none !important; }
        .tabulator .tabulator-header .tabulator-col .tabulator-col-title { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; color:#64748b; }
        .tabulator .tabulator-row { border-bottom:1px solid #f1f5f9 !important; }
        .tabulator .tabulator-row:hover { background:#f8fafc !important; }
        .tabulator .tabulator-row .tabulator-cell { border-right:none !important; padding:14px 16px !important; font-size:13px; }
        .trend-up { color:#10b981; }
        .trend-down { color:#ef4444; }
        .modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); display:flex; align-items:center; justify-content:center; z-index:50; padding:1rem; }
        .modal-content { background:#fff; border-radius:1rem; box-shadow:0 25px 50px rgba(0,0,0,0.15); width:100%; max-width:28rem; padding:1.5rem; }
        ::-webkit-scrollbar { width:6px; height:6px; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:3px; }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-[260px] flex flex-col fixed h-full z-40" style="background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);">
        <!-- Logo -->
        <div class="px-5 py-5 border-b border-white/8">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 4px 12px rgba(99,102,241,0.4);">
                    <i data-lucide="wallet" class="w-4 h-4 text-white"></i>
                </div>
                <div>
                    <h1 class="text-[15px] font-bold text-white leading-tight">FinPulse</h1>
                    <p class="text-[10px] text-indigo-300/70 font-medium tracking-wide">Financial Analytics</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-5 overflow-y-auto">
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest" style="color:#475569;">Overview</p>
            <div class="space-y-0.5 mb-6">
                <a href="/dashboard" class="sidebar-link <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
                    <span class="nav-icon"><i data-lucide="layout-dashboard"></i></span>
                    <span>Dashboard</span>
                </a>
                <a href="/reports" class="sidebar-link <?php echo e(request()->is('reports') ? 'active' : ''); ?>">
                    <span class="nav-icon"><i data-lucide="bar-chart-3"></i></span>
                    <span>Reports & Analytics</span>
                </a>
            </div>

            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest" style="color:#475569;">Management</p>
            <div class="space-y-0.5">
                <a href="/transactions" class="sidebar-link <?php echo e(request()->is('transactions') ? 'active' : ''); ?>">
                    <span class="nav-icon"><i data-lucide="arrow-left-right"></i></span>
                    <span>Transactions</span>
                </a>
                <a href="/categories" class="sidebar-link <?php echo e(request()->is('categories') ? 'active' : ''); ?>">
                    <span class="nav-icon"><i data-lucide="tags"></i></span>
                    <span>Categories</span>
                </a>
                <a href="/budgets" class="sidebar-link <?php echo e(request()->is('budgets') ? 'active' : ''); ?>">
                    <span class="nav-icon"><i data-lucide="target"></i></span>
                    <span>Budgets</span>
                </a>
            </div>

            <!-- Quick stats -->
            <div class="mt-8 mx-1 p-4 rounded-xl" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);">
                <p class="text-[10px] font-semibold uppercase tracking-widest mb-3" style="color:#64748b;">This Month</p>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center">
                        <span class="text-xs" style="color:#94a3b8;">Savings Rate</span>
                        <span class="text-xs font-bold text-emerald-400" id="sb-savings">—</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs" style="color:#94a3b8;">Net Balance</span>
                        <span class="text-xs font-bold text-indigo-300" id="sb-balance">—</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- User -->
        <div class="px-3 py-4 border-t border-white/8">
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.07);">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" style="background: linear-gradient(135deg, #818cf8, #a78bfa);">AM</div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-semibold text-white truncate leading-tight">Alex Morgan</p>
                    <p class="text-[11px] truncate" style="color:#64748b;">test@example.com</p>
                </div>
                <button onclick="logout()" class="flex-shrink-0 p-1.5 rounded-lg transition" style="color:#64748b;" onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.color='#64748b';this.style.background='transparent'" title="Sign out">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1" style="margin-left:260px;">
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30 px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h2>
                    <p class="text-sm text-slate-500 mt-0.5"><?php echo $__env->yieldContent('page-subtitle', ''); ?></p>
                </div>
                <div class="flex items-center gap-3">
                    <?php echo $__env->yieldContent('header-actions'); ?>
                    <span class="text-xs text-slate-400 font-medium bg-slate-100 px-3 py-1.5 rounded-lg"><?php echo e(now()->format('M d, Y')); ?></span>
                </div>
            </div>
        </header>

        <div class="px-8 py-6">
            <?php echo $__env->yieldContent('content'); ?>
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
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        fetch('/api/dashboard/summary', { headers: apiHeaders })
        .then(r => r.json())
        .then(d => {
            const sb = document.getElementById('sb-savings');
            const bb = document.getElementById('sb-balance');
            if (sb) sb.textContent = d.savings_rate + '%';
            if (bb) bb.textContent = fmtShort(d.month_balance);
        }).catch(() => {});
    });
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\Projects\Financial Tracker\resources\views/layouts/app.blade.php ENDPATH**/ ?>