<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — FinPulse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="min-h-screen flex">

    <!-- Left Panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-900 via-indigo-950 to-violet-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 left-20 w-72 h-72 bg-indigo-500 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-violet-500 rounded-full blur-3xl"></div>
        </div>
        <div class="relative z-10 flex flex-col justify-center px-16 text-white">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-2xl shadow-indigo-500/30">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">FinPulse</h1>
                    <p class="text-sm text-indigo-300">Financial Analytics Platform</p>
                </div>
            </div>
            <h2 class="text-4xl font-bold leading-tight mb-4">Take control of your<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">financial future</span></h2>
            <p class="text-indigo-200/80 text-lg max-w-md leading-relaxed">Real-time dashboards, intelligent reporting, and budget tracking — all in one professional platform.</p>
            <div class="mt-12 grid grid-cols-3 gap-6">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10">
                    <p class="text-2xl font-bold">$94K+</p>
                    <p class="text-xs text-indigo-300 mt-1">Tracked annually</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10">
                    <p class="text-2xl font-bold">12</p>
                    <p class="text-xs text-indigo-300 mt-1">Categories</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-5 border border-white/10">
                    <p class="text-2xl font-bold">24%</p>
                    <p class="text-xs text-indigo-300 mt-1">Savings rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="flex-1 flex items-center justify-center px-6 py-12 bg-slate-50">
        <div class="w-full max-w-md">
            <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5 text-white"></i>
                </div>
                <h1 class="text-xl font-bold text-slate-900">FinPulse</h1>
            </div>

            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">
                <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
                <p class="text-slate-500 text-sm mt-1 mb-8">Sign in to access your financial dashboard</p>

                <form onsubmit="handleLogin(event)" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email address</label>
                        <div class="relative">
                            <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            <input type="email" id="email" required value="test@example.com"
                                class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition"
                                placeholder="you@company.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            <input type="password" id="password" required value="password"
                                class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition"
                                placeholder="Enter your password">
                        </div>
                    </div>
                    <button type="submit" id="login-btn"
                        class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white py-3 rounded-xl font-semibold text-sm shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:from-indigo-500 hover:to-violet-500 transition-all duration-200">
                        Sign in to Dashboard
                    </button>
                </form>

                @if(session('error'))
                <div class="mt-4 text-red-600 text-sm text-center bg-red-50 rounded-lg py-2 px-3">{{ session('error') }}</div>
                @endif
                <div id="error" class="mt-4 text-red-600 text-sm text-center hidden bg-red-50 rounded-lg py-2"></div>

                <div class="mt-8 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Demo credentials</p>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Email</span>
                        <span class="font-mono text-slate-700">test@example.com</span>
                    </div>
                    <div class="flex justify-between text-sm mt-1">
                        <span class="text-slate-500">Password</span>
                        <span class="font-mono text-slate-700">password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        if (localStorage.getItem('auth_token')) window.location.href = '/dashboard';

        async function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('login-btn');
            const err = document.getElementById('error');
            btn.textContent = 'Signing in...';
            btn.disabled = true;
            err.classList.add('hidden');
            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                    })
                });
                if (!res.ok) throw new Error('Invalid credentials. Please try again.');
                const data = await res.json();
                localStorage.setItem('auth_token', data.token);
                window.location.href = '/dashboard';
            } catch (ex) {
                err.textContent = ex.message;
                err.classList.remove('hidden');
                btn.textContent = 'Sign in to Dashboard';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
