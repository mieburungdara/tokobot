<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TokoBot Mini App</title>
    <!-- 1. SERTAKAN SCRIPT TELEGRAM -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding: 15px; color: var(--tg-theme-text-color); background-color: var(--tg-theme-bg-color); }
        #user-info, #status { margin-top: 20px; padding: 10px; border-radius: 8px; background-color: var(--tg-theme-secondary-bg-color); }
        pre { white-space: pre-wrap; word-wrap: break-word; background: #333; color: #eee; padding: 10px; border-radius: 5px; }
        .hidden { display: none; }
    </style>
</head>
<body>

    <h1>Selamat Datang di Mini App!</h1>
    <p>Status Otentikasi:</p>
    <div id="status">Menunggu validasi...</div>

    <div id="user-info" class="hidden">
        <h2>Data Pengguna (Tervalidasi)</h2>
        <pre id="user-json"></pre>
    </div>

    <div id="init-data-raw" class="hidden">
        <h3>Raw InitData:</h3>
        <pre id="init-data-pre"></pre>
    </div>

    <script>
        // Sisipkan bot_id dari controller PHP ke JavaScript
        const botId = <?= json_encode($bot_id) ?>;

        // 2. INISIALISASI TELEGRAM WEB APP
        const tg = window.Telegram.WebApp;
        tg.ready(); // Memberitahu Telegram bahwa aplikasi siap
        tg.expand(); // Memperluas tampilan Mini App

        // Fungsi untuk mengirim data ke backend
        async function authenticateUser() {
            const statusDiv = document.getElementById('status');
            
            // 3. AMBIL INITDATA
            const initData = tg.initData;

            if (!initData) {
                statusDiv.textContent = 'Error: initData tidak ditemukan. Aplikasi ini harus dibuka dari dalam Telegram.';
                statusDiv.style.color = 'red';
                return;
            }

            // Tampilkan raw initData untuk debug
            document.getElementById('init-data-pre').textContent = initData;
            document.getElementById('init-data-raw').classList.remove('hidden');

            try {
                statusDiv.textContent = 'Mengirim data ke server untuk validasi...';

                // 4. KIRIM INITDATA & BOT_ID KE BACKEND API
                const response = await fetch('/api/miniapp/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json' // Kirim sebagai JSON
                    },
                    body: JSON.stringify({
                        initData: initData,
                        bot_id: botId
                    })
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    statusDiv.textContent = 'Berhasil! Pengguna telah diautentikasi oleh server.';
                    statusDiv.style.color = 'green';

                    // 5. TAMPILKAN DATA PENGGUNA YANG DITERIMA DARI BACKEND
                    const userInfoDiv = document.getElementById('user-info');
                    const userJsonPre = document.getElementById('user-json');
                    userJsonPre.textContent = JSON.stringify(result.user_data, null, 2);
                    userInfoDiv.classList.remove('hidden');

                    // Kirim notifikasi ke user di dalam chat Telegram
                    tg.showAlert(`Halo, ${result.user_data.first_name}! Anda berhasil diautentikasi.`);

                } else {
                    throw new Error(result.message || 'Validasi di server gagal.');
                }

            } catch (error) {
                statusDiv.textContent = `Error: ${error.message}`;
                statusDiv.style.color = 'red';
                tg.showAlert(`Otentikasi gagal: ${error.message}`);
            }
        }

        // Panggil fungsi otentikasi saat halaman dimuat
        authenticateUser();

    </script>

</body>
</html>
