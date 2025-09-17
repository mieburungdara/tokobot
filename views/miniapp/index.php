<?php
// File ini hanya berisi konten dan logika JavaScript untuk Mini App.
// Kerangka HTML, head, dan body disediakan oleh miniapp_layout.php
?>
<style>
    #user-info, #status { margin-top: 20px; padding: 10px; border-radius: 8px; background-color: var(--tg-theme-secondary-bg-color); }
    pre { white-space: pre-wrap; word-wrap: break-word; background: #333; color: #eee; padding: 10px; border-radius: 5px; }
    .hidden { display: none; }
</style>

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
    // Ambil bot_id dari variabel PHP yang di-extract oleh renderDashmix
    const botId = <?= json_encode($bot_id) ?>;

    // INISIALISASI TELEGRAM WEB APP
    const tg = window.Telegram.WebApp;
    tg.ready(); // Memberitahu Telegram bahwa aplikasi siap
    tg.expand(); // Memperluas tampilan Mini App

    // Fungsi untuk mengirim data ke backend
    async function authenticateUser() {
        const statusDiv = document.getElementById('status');
        
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

            // KIRIM INITDATA & BOT_ID KE BACKEND API
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

                // TAMPILKAN DATA PENGGUNA YANG DITERIMA DARI BACKEND
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
