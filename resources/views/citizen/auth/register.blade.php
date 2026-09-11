<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Citizen Registration — Smart Vadodara</title>

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
            margin-bottom: 16px;
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
            margin-top: 5px;
        }

        button:disabled {
            opacity: .65;
        }

        .login {
            text-align: center;
            margin-top: 22px;
            color: #6c7484;
            font-size: 14px;
        }

        .login a {
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

    <h1>Create account</h1>

    <div class="subtitle">
        Join Smart Vadodara and help improve your city.
    </div>

    <div id="error" class="error"></div>
    <div id="success" class="success"></div>

    <form id="registerForm">

        <div class="field">
            <label>Full Name</label>
            <input
                type="text"
                id="name"
                placeholder="Enter your full name"
                required
            >
        </div>

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
                placeholder="Minimum 8 characters"
                minlength="8"
                required
            >
        </div>

        <div class="field">
            <label>Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                placeholder="Confirm your password"
                minlength="8"
                required
            >
        </div>

        <button type="submit" id="registerButton">
            Create Account
        </button>

    </form>

    <div class="login">
        Already have an account?
        <a href="{{ route('citizen.login') }}">Login</a>
    </div>

</div>

<script>
const form = document.getElementById('registerForm');
const button = document.getElementById('registerButton');
const errorBox = document.getElementById('error');
const successBox = document.getElementById('success');

form.addEventListener('submit', async function (event) {

    event.preventDefault();

    errorBox.style.display = 'none';
    successBox.style.display = 'none';

    const password =
        document.getElementById('password').value;

    const confirmation =
        document.getElementById('password_confirmation').value;

    if (password !== confirmation) {
        errorBox.textContent = 'Passwords do not match.';
        errorBox.style.display = 'block';
        return;
    }

    button.disabled = true;
    button.textContent = 'Creating account...';

    try {

        const response = await fetch('/api/auth/register', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: password,
                password_confirmation: confirmation
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message || 'Registration failed.'
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

        successBox.textContent =
            'Account created successfully. Redirecting...';

        successBox.style.display = 'block';

        setTimeout(() => {
            window.location.href = "{{ route('citizen.home') }}";
        }, 500);

    } catch (error) {

        errorBox.textContent = error.message;
        errorBox.style.display = 'block';

    } finally {

        button.disabled = false;
        button.textContent = 'Create Account';
    }
});
</script>

</body>
</html>
