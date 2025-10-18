<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Disaster Alert System</title>
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
            padding: 2rem 0;
        }
        .register-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            margin: 1rem;
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .register-subtitle {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2c3e50;
            font-size: 0.9rem;
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
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            background-color: white;
        }
        .form-select:focus {
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
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover {
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .info-notice {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #0d47a1;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="back-link">
            <a href="{{ route('dashboard') }}">← Back to Public Dashboard</a>
        </div>

        <div class="register-header">
            <div class="register-title">🚨 Disaster Alert System</div>
            <div class="register-subtitle">Create Your Account</div>
        </div>

        <div class="emergency-notice">
            <strong>⚠️ For immediate emergencies call 999</strong>
        </div>

        <div class="info-notice">
            ℹ️ Register to submit emergency requests and track your status in real-time.
        </div>

        @if($errors->any())
            <div class="error-message">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('auth.register') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-input" 
                       placeholder="Enter your full name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-input" 
                       placeholder="Enter your email" value="{{ old('email') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="Minimum 6 characters" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                           placeholder="Re-enter password" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" class="form-input" 
                       placeholder="+880 1234567890" value="{{ old('phone') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <input type="text" id="address" name="address" class="form-input" 
                       placeholder="Your full address" value="{{ old('address') }}">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <input type="text" id="city" name="city" class="form-input" 
                           placeholder="Dhaka" value="{{ old('city') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-input" 
                           placeholder="1000" value="{{ old('postal_code') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="role">Register As *</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="citizen" {{ old('role') == 'citizen' ? 'selected' : '' }}>Citizen (Request Help)</option>
                    <option value="relief_worker" {{ old('role') == 'relief_worker' ? 'selected' : '' }}>Relief Worker (Volunteer)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                ✅ Create Account
            </button>
        </form>

        <div class="login-link">
            Already have an account? 
            <a href="{{ route('login') }}">Login Here</a>
        </div>

        <!-- Quick Actions -->
        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #eee;">
            <div style="margin-bottom: 0.5rem; color: #7f8c8d; font-size: 0.9rem;">Quick Actions (No Registration Required)</div>
            <a href="{{ route('alerts.index') }}" style="color: #e74c3c; text-decoration: none; margin: 0 0.5rem;">View Alerts</a> |
            <a href="{{ route('shelters.index') }}" style="color: #27ae60; text-decoration: none; margin: 0 0.5rem;">Find Shelters</a> |
            <a href="{{ route('requests.create') }}" style="color: #f39c12; text-decoration: none; margin: 0 0.5rem;">Emergency Help</a>
        </div>
    </div>
</body>
</html>
