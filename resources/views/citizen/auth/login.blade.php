<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Login — Smart Vadodara</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f7fb;
            color: #172033;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 430px;
            background: white;
            border-radius: 24px;
            padding: 32px 26px;
            box-shadow: 0 15px 50px rgba(20, 35, 70, .10);
        }

        .logo {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: #1769e0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .subtitle {
            color: #6c7484;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .field {
            margin-bottom: 18px;
        }

        input {
            width: 100%;
            height: 50px;
            border: 1px solid #dce1ea;
            border-radius: 12px;
            padding: 0 15px;
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: #1769e0;
            box-shadow: 0 0 0 3px rgba(23, 105, 224, .10);
        }

        button {
            width: 100%;
            height: 52px;
            border: 0;
            border-radius: 13px;
            background: #1769e0;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        button:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .register {
            text-align: center;
            margin-top: 22px;
            color: #6c7484;
            font-size: 14px;
        }

        .register a {
            color: #1769e0;
            font-weight: 700;
            text-decoration: none;
        }

        .error {
            display: none;
            background: #fff1f1;
            color: #c62828;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .success {
            display: none;
            background: #effaf2;
            color: #237a35;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="logo">SV</div>

    <h1>Welcome back</h1>

    <div class="subtitle">
        Login to report and track civic issues in Vadodara.
    </div>

    <div id="error" class="error"></div>
    <div id="success" class="success"></div>

    <form id="loginForm">

        <div class="field">
            <label>Email</label>
            <input
                type="email"
                id="email"
                placeholder="Enter your email"
                required
            >
        </div>

        <div class="field">
            <label>Password</label>
            <input
                type="password"
                id="password"
                placeholder="Enter your password"
                required
            >
        </div>

        <button type="submit" id="loginButton">
            Login
        </button>

    </form>

    <div class="register">
        Don't have an account?
        <a href="{{ route('citizen.register') }}">Create account</a>
    </div>

</div>

<script>
const form = document.getElementById('loginForm');
const button = document.getElementById('loginButton');
const errorBox = document.getElementById('error');
const successBox = document.getElementById('success');

form.addEventListener('submit', async function (event) {

    event.preventDefault();

    errorBox.style.display = 'none';
    successBox.style.display = 'none';

    button.disabled = true;
    button.textContent = 'Logging in...';

    try {

        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message || 'Invalid email or password.'
            );
        }

        if (data.user.role !== 'citizen') {
            throw new Error(
                'This login is only for citizens.'
            );
        }

        localStorage.setItem(
            'smart_vadodara_token',
            data.token
        );

        localStorage.setItem(
            'smart_vadodara_user',
            JSON.stringify(data.user)
        );

        successBox.textContent = 'Login successful. Redirecting...';
        successBox.style.display = 'block';

        setTimeout(() => {
            window.location.href = "{{ route('citizen.home') }}";
        }, 500);

    } catch (error) {

        errorBox.textContent = error.message;
        errorBox.style.display = 'block';

    } finally {

        button.disabled = false;
        button.textContent = 'Login';
    }
});
</script>

</body>
</html>
