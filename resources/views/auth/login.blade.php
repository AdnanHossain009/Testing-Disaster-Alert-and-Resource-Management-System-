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
            background: #0D1326;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #091F57;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(43, 85, 189, 0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            color: #E4E8F5;
            opacity: 0.7;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #E4E8F5;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid rgba(43, 85, 189, 0.4);
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            background-color: #0D1326;
            color: #E4E8F5;
        }
        .form-input:focus {
            outline: none;
            border-color: #2B55BD;
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
            transition: all 0.3s;
        }
        .btn-primary {
            background: #2B55BD;
            color: white;
            box-shadow: 0 4px 12px rgba(43, 85, 189, 0.4);
        }
        .btn-primary:hover {
            background-color: #3d6fd4;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(43, 85, 189, 0.6);
        }
        .demo-accounts {
            background-color: rgba(43, 85, 189, 0.1);
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
            font-size: 0.9rem;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .demo-account {
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: rgba(43, 85, 189, 0.15);
            border-radius: 4px;
            border-left: 4px solid #2B55BD;
            color: #E4E8F5;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
        }
        .register-link a {
            color: #2B55BD;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .register-link a:hover {
            color: #3d6fd4;
            text-decoration: underline;
        }
        .emergency-notice {
            background-color: rgba(255, 107, 107, 0.15);
            border: 1px solid #ff6b6b;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #ff6b6b;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-bottom: 1rem;
        }
        .back-link a {
            color: #2B55BD;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link a:hover {
            color: #3d6fd4;
        }
        .error-message {
            background-color: rgba(255, 107, 107, 0.15);
            border: 1px solid #ff6b6b;
            color: #ff6b6b;
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
