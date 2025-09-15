<?php
/**
 * get_started/landing/views/inc_header.php
 *
 * Author: pixelcave
 *
 * The header of each page
 *
 */
?>

<!-- Header -->
<header id="page-header">
  <!-- Header Content -->
  <div class="content-header">
    <!-- Left Section -->
    <div class="d-flex align-items-center">
      <!-- Logo -->
      <a class="link-fx fs-lg fw-semibold text-dark" href="/">
        Toko<span class="text-primary">Bot</span>
      </a>
      <!-- END Logo -->
    </div>
    <!-- END Left Section -->

    <!-- Right Section -->
    <div class="d-flex align-items-center">
      <!-- Menu -->
      <div class="d-none d-lg-block">
        <ul class="nav-main nav-main-horizontal nav-main-hover">
          <?php $dm->build_nav(false, true); ?>
        </ul>
      </div>
      <!-- END Menu -->

      <!-- Login/Logout Button -->
      <div class="ms-2">
        <?php if (\TokoBot\Helpers\Session::get('user_role', 'guest') !== 'guest'): ?>
          <a class="btn btn-alt-primary" href="/dashboard">
            <i class="fa fa-fw fa-user-check"></i>
            <span class="d-none d-sm-inline-block">Dashboard</span>
          </a>
        <?php else: ?>
          <a class="btn btn-alt-primary" href="/xoradmin">
            <i class="fa fa-fw fa-sign-in-alt"></i>
            <span class="d-none d-sm-inline-block">Log In</span>
          </a>
        <?php endif; ?>
      </div>
      <!-- END Login/Logout Button -->

      <!-- Toggle Sidebar -->
      <button type="button" class="btn btn-alt-secondary d-lg-none ms-1" data-toggle="layout" data-action="sidebar_toggle">
        <i class="fa fa-fw fa-bars"></i>
      </button>
      <!-- END Toggle Sidebar -->
    </div>
    <!-- END Right Section -->
  </div>
  <!-- END Header Content -->

  <!-- Header Search -->
  <div id="page-header-search" class="overlay-header bg-primary">
    <div class="content-header">
      <form class="w-100" method="POST">
        <div class="input-group">
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
          <button type="button" class="btn btn-primary" data-toggle="layout" data-action="header_search_off">
            <i class="fa fa-fw fa-times-circle"></i>
          </button>
          <input type="text" class="form-control border-0" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
        </div>
      </form>
    </div>
  </div>
  <!-- END Header Search -->

  <!-- Header Loader -->
  <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
  <div id="page-header-loader" class="overlay-header bg-primary-darker">
    <div class="content-header">
      <div class="w-100 text-center">
        <i class="fa fa-fw fa-2x fa-sun fa-spin text-white"></i>
      </div>
    </div>
  </div>
  <!-- END Header Loader -->
</header>
<!-- END Header -->
