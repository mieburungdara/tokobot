<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title"><?php echo $pageTitle; ?></h3>
        </div>
        <div class="block-content block-content-full">
            <?php if (\TokoBot\Helpers\Session::has('success_message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo \TokoBot\Helpers\Session::flash('success_message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (\TokoBot\Helpers\Session::has('error_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo \TokoBot\Helpers\Session::flash('error_message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?php echo $formAction; ?>" method="POST">
                <div class="row push">
                    <div class="col-lg-8 col-xl-5">
                        <div class="mb-4">
                            <label class="form-label" for="bot_id">Bot</label>
                            <select class="form-select" id="bot_id" name="bot_id">
                                <option value="">Select a Bot</option>
                                <?php foreach ($bots as $bot): ?>
                                    <option value="<?php echo htmlspecialchars($bot['id']); ?>"
                                        <?php echo (isset($channel['bot_id']) && $channel['bot_id'] == $bot['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($bot['username'] ?? $bot['id']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="channel_id">
                                Channel ID
                                <i class="fa fa-question-circle ms-1" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modal-get-channel-id" title="Cara mendapatkan ID channel private"></i>
                            </label>
                            <input type="text" class="form-control" id="channel_id" name="channel_id" placeholder="Enter Telegram Channel ID (e.g., -1001234567890)" value="<?php echo htmlspecialchars($channel['channel_id'] ?? ''); ?>">
                        </div>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-alt-primary">Submit</button>
                            <a href="/storage-channels" class="btn btn-alt-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Get Channel ID Help Modal -->
<div class="modal" id="modal-get-channel-id" tabindex="-1" role="dialog" aria-labelledby="modal-get-channel-id" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-rounded block-transparent mb-0">
        <div class="block-header block-header-default">
          <h3 class="block-title">Cara Mendapatkan ID Channel Private</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa fa-fw fa-times"></i>
            </button>
          </div>
        </div>
        <div class="block-content fs-sm">
          <p>ID untuk channel private tidak terlihat langsung di aplikasi Telegram. Gunakan salah satu metode berikut untuk mendapatkannya:</p>
          
          <h5 class="mt-4">Metode 1: Menggunakan Bot Pihak Ketiga (Contoh: @userinfobot)</h5>
          <ol>
            <li>Forward (teruskan) pesan <strong>apapun</strong> dari channel private Anda ke bot <code>@userinfobot</code>.</li>
            <li>Bot tersebut akan membalas dengan informasi, termasuk "Forwarded from channel" beserta ID-nya.</li>
            <li>Salin ID channel tersebut (contoh: <code>-1001234567890</code>) dan tempelkan di kolom 'Channel ID'.</li>
          </ol>

          <h5 class="mt-4">Metode 2: Menggunakan Bot Anda Sendiri</h5>
          <p>Jika bot Anda memiliki perintah untuk menampilkan ID chat (misalnya <code>/getid</code>):</p>
          <ol>
            <li>Jadikan bot Anda sebagai <strong>Administrator</strong> di channel private.</li>
            <li>Kirim perintah <code>/getid</code> di channel tersebut.</li>
            <li>Bot akan membalas dengan ID channel. Salin dan tempelkan di sini.</li>
          </ol>
          
          <div class="alert alert-info mt-4">
            <i class="fa fa-info-circle me-1"></i> <strong>Penting:</strong> ID channel private biasanya diawali dengan <code>-100...</code>.
          </div>
        </div>
        <div class="block-content block-content-full text-end bg-body">
            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
</div>
