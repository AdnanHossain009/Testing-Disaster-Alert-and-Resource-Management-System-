<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - Disaster Alert System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .error-container {
            background: white;
            padding: 3rem 2rem;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .error-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .error-code {
            font-size: 3rem;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .error-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .btn {
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn-secondary:hover {
            background: #f8f9fa;
        }
        .help-text {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
            color: #95a5a6;
            font-size: 0.9rem;
        }
        .countdown {
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⏱️</div>
        <div class="error-code">419</div>
        <div class="error-title">Session Expired</div>
        
        <div class="error-message">
            <strong>⚠️ Your session has expired for security reasons.</strong><br>
            This can happen when:
            <ul style="text-align: left; margin-top: 0.5rem; padding-left: 2rem;">
                <li>You've been inactive for too long</li>
                <li>Your browser was idle on the login page</li>
                <li>You opened the page in multiple tabs</li>
            </ul>
        </div>

        <div class="error-actions">
            <button onclick="refreshAndRedirect()" class="btn btn-primary">
                🔄 Refresh & Try Again
            </button>
            <a href="{{ route('login') }}" class="btn btn-secondary">
                🏠 Back to Login
            </a>
        </div>

        <div class="help-text">
            🔒 Your security is important to us.<br>
            Auto-redirecting in <span class="countdown" id="countdown">5</span> seconds...
        </div>
    </div>

    <script>
        // Auto-redirect countdown
        let seconds = 5;
        const countdownEl = document.getElementById('countdown');
        
        const countdown = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = '{{ route("login") }}';
            }
        }, 1000);

        // Manual refresh and redirect
        function refreshAndRedirect() {
            // Clear any cached data
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => caches.delete(name));
                });
            }
            
            // Force hard reload and redirect
            window.location.href = '{{ route("login") }}?refresh=' + Date.now();
        }

        // Prevent back button issues
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, null, window.location.href);
        };
    </script>
</body>
</html>
