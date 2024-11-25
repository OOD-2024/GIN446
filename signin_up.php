<?php
require_once 'includes/config_session.inc.php';

require_once 'includes/signup_view.inc.php';

require_once 'includes/login_view.inc.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .container {
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            color: #1a73e8;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.9rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        input,
        select {
            width: 100%;
            padding: 0.8rem;
            border: 1.5px solid #e1e1e1;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #1a73e8;
        }

        button {
            width: 100%;
            padding: 0.9rem;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
        }

        button:hover {
            background: #1557b0;
        }

        button:active {
            transform: scale(0.98);
        }

        .secondary-button {
            background: transparent;
            color: #1a73e8;
            border: 1.5px solid #1a73e8;
            margin-top: 1rem;
        }

        .secondary-button:hover {
            background: #f0f7ff;
        }

        #signup-form {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px);
        }

        #signup-form.active {
            opacity: 1;
            pointer-events: all;
            transform: translateY(0);
        }

        #login-form.hidden {
            opacity: 0;
            pointer-events: none;
            transform: translateY(-20px);
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .error {
            border-color: #dc3545;
            animation: shake 0.2s ease-in-out;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.3rem;
            display: none;
        }

        .error-message.visible {
            display: block;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container" id="login-form">
            <div class="form-header">
                <h1>Welcome Back</h1>
            </div>
            <form action="includes/login.inc.php" method="post">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                    <div class="error-message">Please enter a valid email address</div>
                </div>
                <div class="form-group">
                    <input type="password" name="pwd" placeholder="Password" required>
                    <div class="error-message">Password is required</div>
                </div>
                <button type="submit">Login</button>
            </form>
            <button class="secondary-button" onclick="toggleForms()">Create Account</button>
        </div>
        <?php
        check_login_errors();
        ?>

        <div class="form-container" id="signup-form">
            <div class="form-header">
                <h1>Create Account</h1>
            </div>
            <form action="includes/signup.inc.php" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" name="Fname" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="Lname" placeholder="Last Name" required>
                    </div>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="pwd" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="DOB" required>
                </div>
                <div class="form-group">
                    <select name="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" name="BloodType" maxlength="3" pattern="([AaBbOo]|[Aa][Bb])[\+-]"
                        placeholder="Blood Type (e.g., A+, B-)" required>
                </div>
                <button type="submit">Sign Up</button>
            </form>
            <button class="secondary-button" onclick="toggleForms()">Back to Login</button>
        </div>
    </div>

    <script>

        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');

            if (loginForm.classList.contains('hidden')) {
                // Switch to login
                loginForm.classList.remove('hidden');
                signupForm.classList.remove('active');
            } else {
                // Switch to signup
                loginForm.classList.add('hidden');
                signupForm.classList.add('active');
            }
        }

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function (e) {
                let hasError = false;

                // Clear previous errors
                form.querySelectorAll('.error').forEach(field => {
                    field.classList.remove('error');
                });
                form.querySelectorAll('.error-message.visible').forEach(msg => {
                    msg.classList.remove('visible');
                });

                // Validate email
                const emailInput = form.querySelector('input[type="email"]');
                if (emailInput && !isValidEmail(emailInput.value)) {
                    showError(emailInput);
                    hasError = true;
                }

                // Validate password
                const pwdInput = form.querySelector('input[type="password"]');
                if (pwdInput && pwdInput.value.length < 6) {
                    showError(pwdInput);
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                }
            });
        });

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function showError(input) {
            input.classList.add('error');
            const errorMessage = input.nextElementSibling;
            if (errorMessage && errorMessage.classList.contains('error-message')) {
                errorMessage.classList.add('visible');
            }
        }

    </script>
    <?php
    check_signup_errors();
    ?>
</body>

</html>