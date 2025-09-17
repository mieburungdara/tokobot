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
    <p id="status-text">Menunggu validasi...</p>
  </div>
</div>

<!-- Blok Data Pengguna (disembunyikan awalnya) -->
<div id="user-info-block" class="block block-rounded d-none">
  <div class="block-header block-header-default">
    <h3 class="block-title">Data Pengguna Tervalidasi</h3>
  </div>
  <div class="block-content">
    <!-- Menggunakan <pre> di dalam block akan otomatis diberi style oleh Dashmix/Bootstrap -->
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
    // Ambil bot_id dari variabel PHP yang di-extract oleh renderDashmix
    const botId = <?= json_encode($bot_id) ?>;

    // INISIALISASI TELEGRAM WEB APP
    const tg = window.Telegram.WebApp;
    tg.ready();
    tg.expand();

    // Fungsi untuk mengirim data ke backend
    async function authenticateUser() {
        const statusText = document.getElementById('status-text');
        const initData = tg.initData;

        if (!initData) {
            statusText.textContent = 'Error: initData tidak ditemukan. Aplikasi ini harus dibuka dari dalam Telegram.';
            statusText.parentElement.classList.add('text-danger');
            return;
        }

        // Tampilkan raw initData untuk debug
        document.getElementById('init-data-pre').textContent = initData;
        document.getElementById('init-data-raw-block').classList.remove('d-none');

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

    // Panggil fungsi otentikasi saat halaman dimuat
    authenticateUser();
</script>

