<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Disaster Alert System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .demo-accounts {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
            font-size: 0.9rem;
        }
        .demo-account {
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: white;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
        }
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .emergency-notice {
            background-color: #fdf2f2;
            border: 1px solid #e74c3c;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #721c24;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-bottom: 1rem;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
        }
        .error-message {
            background-color: #fdf2f2;
            border: 1px solid #e74c3c;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="back-link">
            <a href="{{ route('dashboard') }}">← Back to Public Dashboard</a>
        </div>

        <div class="login-header">
            <div class="login-title">🚨 Disaster Alert System</div>
            <div class="login-subtitle">Secure Login Portal</div>
        </div>

        <div class="emergency-notice">
            <strong>⚠️ For immediate emergencies call 999</strong>
        </div>

        @if($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('auth.login') }}" method="POST" id="loginForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" 
                       placeholder="Enter your email" required value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" 
                       placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-primary" id="loginBtn">
                🔐 Login to System
            </button>
        </form>

        <script>
            // Prevent 419 errors by refreshing CSRF token and handling form submission
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('loginForm');
                const loginBtn = document.getElementById('loginBtn');
                
                // Refresh CSRF token before form submission
                form.addEventListener('submit', function(e) {
                    // Disable button to prevent double submission
                    loginBtn.disabled = true;
                    loginBtn.textContent = '🔄 Logging in...';
                    
                    // Re-enable after 3 seconds in case of error
                    setTimeout(() => {
                        loginBtn.disabled = false;
                        loginBtn.textContent = '🔐 Login to System';
                    }, 3000);
                });

                // Auto-fill demo credentials
                document.querySelectorAll('.demo-account').forEach(account => {
                    account.style.cursor = 'pointer';
                    account.addEventListener('click', function() {
                        const text = this.textContent;
                        let email = '';
                        
                        if (text.includes('admin@disaster.gov.bd')) {
                            email = 'admin@disaster.gov.bd';
                            document.getElementById('password').value = 'admin123';
                        } else if (text.includes('citizen@example.com')) {
                            email = 'citizen@example.com';
                            document.getElementById('password').value = 'citizen123';
                        } else if (text.includes('relief@disaster.gov.bd')) {
                            email = 'relief@disaster.gov.bd';
                            document.getElementById('password').value = 'relief123';
                        }
                        
                        document.getElementById('email').value = email;
                        document.getElementById('email').focus();
                    });
                });
            });
        </script>

        <!-- Demo Accounts -->
        <div class="demo-accounts">
            <strong>🧪 Demo Accounts (For Testing)</strong>
            
            <div class="demo-account">
                <strong>Admin:</strong> admin@disaster.gov.bd<br>
                <strong>Password:</strong> admin123
            </div>
            
            <div class="demo-account">
                <strong>Citizen:</strong> citizen@example.com<br>
                <strong>Password:</strong> citizen123
            </div>
            
            <div class="demo-account">
                <strong>Relief Worker:</strong> relief@disaster.gov.bd<br>
                <strong>Password:</strong> relief123
            </div>
        </div>

        <div class="register-link">
            Don't have an account? 
            <a href="{{ route('auth.register') }}">Register as Citizen</a>
        </div>

        <!-- Quick Actions -->
        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #eee;">
            <div style="margin-bottom: 0.5rem; color: #7f8c8d; font-size: 0.9rem;">Quick Actions (No Login Required)</div>
            <a href="{{ route('alerts.index') }}" style="color: #e74c3c; text-decoration: none; margin: 0 0.5rem;">View Alerts</a> |
            <a href="{{ route('shelters.index') }}" style="color: #27ae60; text-decoration: none; margin: 0 0.5rem;">Find Shelters</a> |
            <a href="{{ route('requests.create') }}" style="color: #f39c12; text-decoration: none; margin: 0 0.5rem;">Emergency Help</a>
        </div>
    </div>
</body>
</html>
