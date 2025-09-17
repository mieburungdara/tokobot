<?php
/**
 * backend/views/inc_side_overlay.php
 *
 * Author: pixelcave
 *
 * The side overlay of each page (Backend pages)
 *
 */
?>
<!-- Side Overlay-->
<aside id="side-overlay">
  <!-- Side Header -->
  <div class="bg-image" style="background-image: url('<?php echo $dm->assets_folder; ?>/media/various/bg_side_overlay_header.jpg');">
    <div class="bg-primary-op">
      <div class="content-header">
        <?php
        // Cek jika ada sesi pengguna
        if (\TokoBot\Helpers\Session::get('user_role', 'guest') !== 'guest') : 
          // Ambil data pengguna dari session
          $currentUser = \TokoBot\Helpers\Session::get('user');
        ?>
        <!-- User Avatar -->
        <a class="img-link me-1" href="javascript:void(0)">
          <?php if (isset($currentUser['photo_url']) && !empty($currentUser['photo_url'])) : ?>
            <img class="img-avatar img-avatar48" src="<?= htmlspecialchars($currentUser['photo_url']) ?>" alt="User Photo">
          <?php else: ?>
            <?php $dm->get_avatar(0, '', 48); ?>
          <?php endif; ?>
        </a>
        <!-- END User Avatar -->

        <!-- User Info -->
        <div class="ms-2">
          <a class="text-white fw-semibold" href="javascript:void(0)"><?= isset($currentUser['first_name']) ? htmlspecialchars($currentUser['first_name'] . ' ' . ($currentUser['last_name'] ?? '')) : 'Guest' ?></a>
          <div class="text-white-75 fs-sm"><?= isset($currentUser['role']) ? ucfirst(htmlspecialchars($currentUser['role'])) : '' ?></div>
        </div>
        <!-- END User Info -->
        <?php endif; ?>

        <!-- Close Side Overlay -->
        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
        <a class="ms-auto text-white" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_close">
          <i class="fa fa-times-circle"></i>
        </a>
        <!-- END Close Side Overlay -->
      </div>
    </div>
  </div>
  <!-- END Side Header -->

  <!-- Side Content -->
  <div class="content-side">
    <!-- Side Overlay Tabs -->
    <div class="block block-transparent pull-x pull-t mb-0">
      <ul class="nav nav-tabs nav-tabs-block nav-justified" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="so-profile-tab" data-bs-toggle="tab" data-bs-target="#so-profile" role="tab" aria-controls="so-profile" aria-selected="false">
            <i class="fa fa-fw fa-robot"></i>
          </button>
        </li>
      </ul>
      <div class="block-content tab-content overflow-hidden">

        <!-- Profile -->
        <div class="tab-pane pull-x fade fade-up" id="so-profile" role="tabpanel" aria-labelledby="so-profile-tab" tabindex="0">
          <div class="block mb-0">
            <div class="block-content block-content-sm block-content-full bg-body">
              <span class="text-uppercase fs-sm fw-bold">All Bots</span>
            </div>
            <div class="block-content">
              <ul class="nav-items">
                <?php
                try {
                    $pdo = \TokoBot\Helpers\Database::getInstance();
                    
                    // 1. Ambil data izin spesifik untuk pengguna yang sedang login
                    $userBotPermissions = [];
                    $currentUserId = \TokoBot\Helpers\Session::get('user')['telegram_id'] ?? null;

                    if ($currentUserId) {
                        $stmtPermissions = $pdo->prepare("SELECT bot_id, allows_write_to_pm FROM bot_user WHERE user_id = ?");
                        $stmtPermissions->execute([$currentUserId]);
                        $permissions = $stmtPermissions->fetchAll(\PDO::FETCH_KEY_PAIR);
                    }

                    // 2. Ambil semua data bot
                    $stmtBots = $pdo->query("SELECT id, username, first_name FROM tbots ORDER BY first_name ASC");
                    $allBots = $stmtBots->fetchAll();

                    if (empty($allBots)) {
                        echo '<li class="text-muted fs-sm p-2">No bots found.</li>';
                    } else {
                        foreach ($allBots as $bot) {
                            // 3. Tentukan status centang
                            $allowWrite = isset($permissions[$bot['id']]) && $permissions[$bot['id']];
                ?>
                <li>
                  <a class="d-flex py-2" href="/bot-management">
                    <div class="flex-shrink-0 mx-3">
                      <?php if ($allowWrite): ?>
                        <i class="fa fa-2x fa-check-circle text-success"></i>
                      <?php else: ?>
                        <i class="fa fa-2x fa-times-circle text-danger"></i>
                      <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                      <div class="fw-semibold"><?= htmlspecialchars($bot['first_name']) ?></div>
                      <div class="fs-sm text-muted">@<?= htmlspecialchars($bot['username']) ?></div>
                    </div>
                  </a>
                </li>
                <?php
                        }
                    }
                } catch (\Exception $e) {
                    \TokoBot\Helpers\Logger::channel('app')->error('Failed to fetch bots for side overlay', ['error' => $e->getMessage()]);
                    echo '<li class="text-danger fs-sm p-2">Error loading bots.</li>';
                }
                ?>
              </ul>
            </div>
          </div>
        </div>
        <!-- END Profile -->
      </div>
    </div>
    <!-- END Side Overlay Tabs -->
  </div>
  <!-- END Side Content -->
</aside>
<!-- END Side Overlay -->
