<?php
/**
 * backend/views/inc_header.php
 *
 * Author: pixelcave
 *
 * The header of each page (Backend pages)
 *
 */
?>

<!-- Header -->
<header id="page-header">
  <!-- Header Content -->
  <div class="content-header">
    <!-- Left Section -->
    <div class="space-x-1">
      <!-- Toggle Sidebar -->
      <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
      <button type="button" class="btn btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
        <i class="fa fa-fw fa-bars"></i>
      </button>
      <!-- END Toggle Sidebar -->

      <!-- Open Search Section -->
      <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
      <button type="button" class="btn btn-alt-secondary" data-toggle="layout" data-action="header_search_on">
        <i class="fa fa-fw opacity-50 fa-search"></i> <span class="ms-1 d-none d-sm-inline-block">Search</span>
      </button>
      <!-- END Open Search Section -->
    </div>
    <!-- END Left Section -->

    <!-- Right Section -->
    <div class="space-x-1">

      <!-- DEBUG BLOCK -->
      <?php
      $currentUserForDebug = \TokoBot\Helpers\Session::get('user');
      if (isset($_GET['debug_session']) && $currentUserForDebug) {
          echo '<div style="position: fixed; top: 80px; right: 20px; background: #fff; border: 2px solid red; padding: 15px; z-index: 9999; max-width: 400px; box-shadow: 0 5px 15px rgba(0,0,0,.2));">';
          echo '<h6 style="border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 10px;">DEBUG: Session User Data</h6>';
          echo '<pre style="white-space: pre-wrap; word-wrap: break-word; max-height: 300px; overflow-y: auto;">';
          print_r($currentUserForDebug);
          echo '</pre>';
          echo '</div>';
      }
      ?>
      <!-- END DEBUG BLOCK -->

      <!-- User Dropdown -->
      <?php if (\TokoBot\Helpers\Session::get('user_role', 'guest') !== 'guest'): ?>
      <div class="dropdown d-inline-block">
        <button type="button" class="btn btn-alt-secondary" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php 
            // Ambil data pengguna dari session
            $currentUser = \TokoBot\Helpers\Session::get('user');
            if (isset($currentUser['photo_url']) && !empty($currentUser['photo_url'])) : 
          ?>
            <img class="img-avatar img-avatar32" src="<?= htmlspecialchars($currentUser['photo_url']) ?>" alt="User Photo">
          <?php else: ?>
            <i class="fa fa-fw fa-user d-sm-none"></i>
          <?php endif; ?>
          <span class="d-none d-sm-inline-block"><?= isset($currentUser['first_name']) ? htmlspecialchars($currentUser['first_name']) : ucfirst(\TokoBot\Helpers\Session::get('user_role')); ?></span>
          <i class="fa fa-fw fa-angle-down opacity-50 ms-1 d-none d-sm-inline-block"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
          <div class="bg-primary-dark rounded-top fw-semibold text-white text-center p-3">
            User Options
          </div>
          <div class="p-2">
            <a class="dropdown-item" href="javascript:void(0)">
              <i class="far fa-fw fa-user me-1"></i> Profile
            </a>
            <div role="separator" class="dropdown-divider"></div>
            <a class="dropdown-item" href="/logout">
              <i class="far fa-fw fa-arrow-alt-circle-left me-1"></i> Log Out
            </a>
          </div>
        </div>
      </div>
      <?php else: ?>
      <a class="btn btn-alt-secondary" href="/xoradmin">
        <i class="fa fa-fw fa-sign-in-alt"></i>
        <span class="d-none d-sm-inline-block">Log In</span>
      </a>
      <?php endif; ?>
      <!-- END User Dropdown -->

      <!-- Notifications Dropdown -->
      <div class="dropdown d-inline-block">
        <button type="button" class="btn btn-alt-secondary" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-fw fa-bell"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
          <div class="bg-primary-dark rounded-top fw-semibold text-white text-center p-3">
            Notifications
          </div>
          <ul class="nav-items my-2">
            <li>
              <a class="d-flex text-dark py-2" href="javascript:void(0)">
                <div class="flex-shrink-0 mx-3">
                  <i class="fa fa-fw fa-check-circle text-success"></i>
                </div>
                <div class="flex-grow-1 fs-sm pe-2">
                  <div class="fw-semibold">App was updated to v5.6!</div>
                  <div class="text-muted">3 min ago</div>
                </div>
              </a>
            </li>
            <li>
              <a class="d-flex text-dark py-2" href="javascript:void(0)">
                <div class="flex-shrink-0 mx-3">
                  <i class="fa fa-fw fa-user-plus text-info"></i>
                </div>
                <div class="flex-grow-1 fs-sm pe-2">
                  <div class="fw-semibold">New Subscriber was added! You now have 2580!</div>
                  <div class="text-muted">10 min ago</div>
                </div>
              </a>
            </li>
            <li>
              <a class="d-flex text-dark py-2" href="javascript:void(0)">
                <div class="flex-shrink-0 mx-3">
                  <i class="fa fa-fw fa-times-circle text-danger"></i>
                </div>
                <div class="flex-grow-1 fs-sm pe-2">
                  <div class="fw-semibold">Server backup failed to complete!</div>
                  <div class="text-muted">30 min ago</div>
                </div>
              </a>
            </li>
            <li>
              <a class="d-flex text-dark py-2" href="javascript:void(0)">
                <div class="flex-shrink-0 mx-3">
                  <i class="fa fa-fw fa-exclamation-circle text-warning"></i>
                </div>
                <div class="flex-grow-1 fs-sm pe-2">
                  <div class="fw-semibold">You are running out of space. Please consider upgrading your plan.</div>
                  <div class="text-muted">1 hour ago</div>
                </div>
              </a>
            </li>
            <li>
              <a class="d-flex text-dark py-2" href="javascript:void(0)">
                <div class="flex-shrink-0 mx-3">
                  <i class="fa fa-fw fa-plus-circle text-primary"></i>
                </div>
                <div class="flex-grow-1 fs-sm pe-2">
                  <div class="fw-semibold">New Sale! + $30</div>
                  <div class="text-muted">2 hours ago</div>
                </div>
              </a>
            </li>
          </ul>
          <div class="p-2 border-top">
            <a class="btn btn-alt-primary w-100 text-center" href="javascript:void(0)">
              <i class="fa fa-fw fa-eye opacity-50 me-1"></i> View All
            </a>
          </div>
        </div>
      </div>
      <!-- END Notifications Dropdown -->

      <!-- Toggle Side Overlay -->
      <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
      <button type="button" class="btn btn-alt-secondary" data-toggle="layout" data-action="side_overlay_toggle">
        <i class="far fa-fw fa-list-alt"></i>
      </button>
      <!-- END Toggle Side Overlay -->
    </div>
    <!-- END Right Section -->
  </div>
  <!-- END Header Content -->

  <!-- Header Search -->
  <div id="page-header-search" class="overlay-header bg-header-dark">
    <div class="bg-white-10">
      <div class="content-header">
        <form class="w-100" action="be_pages_generic_search.php" method="POST">
          <div class="input-group">
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-alt-primary" data-toggle="layout" data-action="header_search_off">
              <i class="fa fa-fw fa-times-circle"></i>
            </button>
            <input type="text" class="form-control border-0" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- END Header Search -->

  <!-- Header Loader -->
  <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
  <div id="page-header-loader" class="overlay-header bg-header-dark">
    <div class="bg-white-10">
      <div class="content-header">
        <div class="w-100 text-center">
          <i class="fa fa-fw fa-sun fa-spin text-white"></i>
        </div>
      </div>
    </div>
  </div>
  <!-- END Header Loader -->
</header>
<!-- END Header -->
