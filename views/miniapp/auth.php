<?php
$bot_id = $data['bot_id'] ?? null;
$app_url = '/miniapp/app/' . $bot_id;
$auth_api_url = '/api/miniapp/auth';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authenticating...</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; color: var(--tg-theme-text-color, #000); background-color: var(--tg-theme-bg-color, #fff); }
        .container { text-align: center; }
        .spinner { border: 4px solid rgba(0,0,0,.1); width: 36px; height: 36px; border-radius: 50%; border-left-color: var(--tg-theme-button-color, #007bff); animation: spin 1s ease infinite; margin: 0 auto 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .error { color: var(--tg-theme-destructive-text-color, #ff0000); }
    </style>
</head>
<body>
    <div class="container" id="auth-container">
        <div class="spinner"></div>
        <p>Authenticating your session...</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tg = window.Telegram.WebApp;
            tg.ready();

            const botId = '<?= $bot_id ?>';
            const initData = tg.initData;

            if (!initData) {
                document.getElementById('auth-container').innerHTML = '<p class="error">Authentication failed: Telegram initData is missing.</p>';
                return;
            }

            fetch('<?= $auth_api_url ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    initData: initData,
                    bot_id: botId
                })
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Authentication failed with status: ' + response.status);
                }
            })
            .then(data => {
                if (data.status === 'success') {
                    window.location.replace('<?= addslashes($app_url) ?>');
                } else {
                    throw new Error(data.message || 'Unknown authentication error.');
                }
            })
            .catch(error => {
                console.error('Authentication Error:', error);
                tg.HapticFeedback.notificationOccurred('error');
                document.getElementById('auth-container').innerHTML = `<p class="error"><b>Authentication Failed</b><br><small>${error.message}</small></p><p><a href="#" onclick="location.reload()">Try again</a></p>`;
            });
        });
    </script>
</body>
</html>
