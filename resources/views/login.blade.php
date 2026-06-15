<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Financial Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-600 to-blue-800 min-h-screen flex items-center justify-center">
    
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">💰 FinTracker</h1>
            <p class="text-gray-600 mt-2">Financial Tracker</p>
        </div>

        <form onsubmit="handleLogin(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" required class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="test@example.com" value="test@example.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" required class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password" value="password">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium transition">Login</button>
        </form>

        <div id="error" class="mt-4 text-red-600 text-sm hidden"></div>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p><strong>Demo Credentials:</strong></p>
            <p>Email: test@example.com</p>
            <p>Password: password</p>
        </div>
    </div>

    <script>
        async function handleLogin(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('error');

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                if (!response.ok) {
                    throw new Error('Invalid credentials');
                }

                const data = await response.json();
                localStorage.setItem('auth_token', data.token);
                window.location.href = '/dashboard';
            } catch (e) {
                errorDiv.textContent = e.message;
                errorDiv.classList.remove('hidden');
            }
        }

        // If already logged in, redirect to dashboard
        if (localStorage.getItem('auth_token')) {
            window.location.href = '/dashboard';
        }
    </script>

</body>
</html>
