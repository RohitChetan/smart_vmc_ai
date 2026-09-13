<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login - Smart Vadodara</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Inter, Arial, sans-serif;
            background: #f4f7fb;
            color: #172033;
        }

        .login-card {
            width: min(420px, calc(100% - 30px));
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 35px rgba(0,0,0,.08);
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand h1 {
            margin: 0;
            font-size: 25px;
        }

        .brand p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 13px;
        }

        label {
            display: block;
            margin: 16px 0 7px;
            font-size: 13px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #2563eb;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            border: 0;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .error {
            display: none;
            margin-top: 15px;
            padding: 11px;
            border-radius: 8px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 13px;
        }

        .back {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="brand">
        <h1>Smart Vadodara</h1>
        <p>AI Civic Command Center · Administrator Login</p>
    </div>

    <form id="adminLoginForm">

        <label for="email">Admin Email</label>

        <input
            type="email"
            id="email"
            placeholder="admin@example.com"
            required
            autocomplete="username"
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            placeholder="Enter password"
            required
            autocomplete="current-password"
        >

        <button id="loginButton" type="submit">
            Sign In
        </button>

        <div id="loginError" class="error"></div>

    </form>

    <div class="back">
        Smart Vadodara Civic Management System
    </div>

</div>

<script>
document.getElementById('adminLoginForm').addEventListener('submit', async function (event) {

    event.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const button = document.getElementById('loginButton');
    const errorBox = document.getElementById('loginError');

    errorBox.style.display = 'none';
    button.disabled = true;
    button.innerText = 'Signing in...';

    try {

        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email,
                password
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message ||
                data.errors?.email?.[0] ||
                'Login failed.'
            );
        }

        if (!data.token || !data.user) {
            throw new Error('Invalid authentication response.');
        }

        if (data.user.role !== 'admin') {
            throw new Error(
                'This account does not have administrator access.'
            );
        }

        localStorage.setItem(
            'smart_vadodara_admin_token',
            data.token
        );

        localStorage.setItem(
            'smart_vadodara_admin_user',
            JSON.stringify(data.user)
        );

        window.location.href = '/admin/dashboard';

    } catch (error) {

        console.error(error);

        errorBox.innerText = error.message;
        errorBox.style.display = 'block';

    } finally {

        button.disabled = false;
        button.innerText = 'Sign In';

    }
});
</script>

</body>
</html>
