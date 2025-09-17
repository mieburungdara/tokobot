<?php
// File ini dirender di dalam layout utama Dashmix.
// Menggunakan komponen-komponen Dashmix untuk tampilan yang konsisten.
?>

<!-- Blok Status Otentikasi -->
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">Status Otentikasi</h3>
  </div>
  <div class="block-content">
    <p id="status-text">Menunggu inisialisasi...</p>
    <p id="seller-id-text" class="d-none">ID Penjual: <span id="seller-id-value"></span></p>
  </div>
</div>

<!-- Blok Data Pengguna (disembunyikan awalnya) -->
<div id="user-info-block" class="block block-rounded d-none">
  <div class="block-header block-header-default">
    <h3 class="block-title">Data Pengguna Tervalidasi</h3>
  </div>
  <div class="block-content">
    <pre><code id="user-json-code"></code></pre>
  </div>
</div>

<!-- Blok Debug (disembunyikan awalnya) -->
<div id="init-data-raw-block" class="block block-rounded d-none">
  <div class="block-header block-header-default">
    <h3 class="block-title">Raw InitData (untuk Debug)</h3>
  </div>
  <div class="block-content">
    <pre><code id="init-data-pre"></code></pre>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusText = document.getElementById('status-text');

        // PERIKSA APAKAH APLIKASI DIJALANKAN DI DALAM TELEGRAM
        if (typeof window.Telegram?.WebApp === 'undefined') {
            statusText.textContent = 'Error: Aplikasi ini harus dijalankan dari dalam Telegram.';
            statusText.parentElement.classList.add('text-danger');
            return; // Hentikan eksekusi jika tidak di dalam Telegram
        }

        // Ambil bot_id dari variabel PHP
        const botId = <?= json_encode($bot_id) ?>;
        const sellerId = <?= json_encode($seller_id) ?>;

        if (sellerId) {
            document.getElementById('seller-id-value').textContent = sellerId;
            document.getElementById('seller-id-text').classList.remove('d-none');
        }
        const tg = window.Telegram.WebApp;

        // Inisialisasi Mini App
        tg.ready();
        tg.expand();

        // Panggil fungsi otentikasi
        authenticateUser(tg, botId, statusText);
    });

    // Fungsi untuk mengirim data ke backend
    async function authenticateUser(tg, botId, statusText) {
        const initData = tg.initData;

        if (!initData) {
            statusText.textContent = 'Error: initData tidak ditemukan. Sesi Telegram tidak valid.';
            statusText.parentElement.classList.add('text-danger');
            return;
        }

        // Tampilkan raw initData untuk debug (opsional)
        const initDataBlock = document.getElementById('init-data-raw-block');
        if (initDataBlock) {
            document.getElementById('init-data-pre').textContent = initData;
            // initDataBlock.classList.remove('d-none'); // Uncomment untuk menampilkan blok debug
        }

        try {
            statusText.textContent = 'Mengirim data ke server untuk validasi...';

            // KIRIM INITDATA & BOT_ID KE BACKEND API
            const response = await fetch('/api/miniapp/auth', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    initData: initData,
                    bot_id: botId
                })
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                statusText.textContent = 'Berhasil! Pengguna telah diautentikasi oleh server.';
                statusText.parentElement.classList.add('text-success');

                // TAMPILKAN DATA PENGGUNA DI BLOK YANG SESUAI
                const userInfoBlock = document.getElementById('user-info-block');
                const userJsonCode = document.getElementById('user-json-code');
                userJsonCode.textContent = JSON.stringify(result.user_data, null, 2);
                userInfoBlock.classList.remove('d-none');

                tg.showAlert(`Halo, ${result.user_data.first_name}! Anda berhasil diautentikasi.`);
            } else {
                throw new Error(result.message || 'Validasi di server gagal.');
            }
        } catch (error) {
            statusText.textContent = `Error: ${error.message}`;
            statusText.parentElement.classList.add('text-danger');
            tg.showAlert(`Otentikasi gagal: ${error.message}`);
        }
    }
</script>