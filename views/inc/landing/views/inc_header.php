<?php
/**
 * landing/views/inc_header.php
 *
 * Author: pixelcave
 *
 * The header of landing page
 *
 */
?>

<!-- Header -->
<header id="page-header">
  <!-- Header Content -->
  <div class="content-header justify-content-between">
    <!-- Left Section -->
    <div class="d-flex align-items-center">
      <!-- Logo -->
      <a class="link-fx fs-lg fw-semibold text-dark" href="">
        Dash<span class="text-primary">mix</span>
        <small class="fw-medium"><?php echo $dm->version; ?></small>
      </a>
      <!-- END Logo -->
    </div>
    <!-- END Left Section -->

    <!-- Right Section -->
    <div class="d-flex align-items-center gap-1">
      <!-- Menu -->
      <ul class="nav-main nav-main-horizontal nav-main-hover d-none d-lg-block me-3">
        <li class="nav-main-item">
          <a class="nav-main-link" href="#dm-package">
            <i class="nav-main-link-icon fa fa-box"></i>
            <span class="nav-main-link-name">Package</span>
          </a>
        </li>
        <li class="nav-main-item">
          <a class="nav-main-link" href="#dm-dashboards">
            <i class="nav-main-link-icon fa fa-compass"></i>
            <span class="nav-main-link-name">Dashboards</span>
          </a>
        </li>
        <li class="nav-main-item">
          <a class="nav-main-link" href="#dm-widgets">
            <i class="nav-main-link-icon fa fa-puzzle-piece"></i>
            <span class="nav-main-link-name">Widgets</span>
          </a>
        </li>
        <li class="nav-main-item">
          <a class="nav-main-link" href="#dm-layout">
            <i class="nav-main-link-icon fa fa-fire"></i>
            <span class="nav-main-link-name">Layout</span>
          </a>
        </li>
        <li class="nav-main-item">
          <a class="nav-main-link" href="#dm-toolkit">
            <i class="nav-main-link-icon fab fa-node-js"></i>
            <span class="nav-main-link-name">Toolkit</span>
          </a>
        </li>
        <li class="nav-main-item">
          <a class="nav-main-link" href="#dm-features">
            <i class="nav-main-link-icon fa fa-heartbeat"></i>
            <span class="nav-main-link-name">Features</span>
          </a>
        </li>
      </ul>
      <!-- END Menu -->
    
      <!-- Dark Mode -->
      <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
      <div class="dropdown">
        <button type="button" class="btn btn-sm btn-alt-secondary" id="landing-dark-mode-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="far fa-fw fa-moon" data-dark-mode-icon></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end smini-hide" aria-labelledby="sidebar-dark-mode-dropdown">
          <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_off" data-dark-mode="off">
            <i class="far fa-sun fa-fw opacity-50"></i>
            <span class="fs-sm fw-medium">Light</span>
          </button>
          <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_on" data-dark-mode="on">
            <i class="far fa-moon fa-fw opacity-50"></i>
            <span class="fs-sm fw-medium">Dark</span>
          </button>
          <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_system" data-dark-mode="system">
            <i class="fa fa-desktop fa-fw opacity-50"></i>
            <span class="fs-sm fw-medium">System</span>
          </button>
        </div>
      </div>
      <!-- END Dark Mode -->

      <!-- Options -->
      <div class="dropdown">
        <button type="button" class="btn btn-sm btn-alt-secondary" id="landing-themes-dropdown" data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-fw fa-paint-brush"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end fs-sm" aria-labelledby="landing-themes-dropdown">
          <!-- Color Themes -->
          <!-- Layout API, functionality initialized in Template._uiHandleTheme() -->
          <div class="row g-sm text-center">
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-default rounded-1" data-toggle="theme" data-theme="default" href="#">
                Default
              </a>
            </div>
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-xwork rounded-1" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xwork.min.css" href="#">
                xWork
              </a>
            </div>
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-xmodern rounded-1" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xmodern.min.css" href="#">
                xModern
              </a>
            </div>
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-xeco rounded-1" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xeco.min.css" href="#">
                xEco
              </a>
            </div>
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-xsmooth rounded-1" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xsmooth.min.css" href="#">
                xSmooth
              </a>
            </div>
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-xinspire rounded-1" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xinspire.min.css" href="#">
                xInspire
              </a>
            </div>
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-xdream rounded-1" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xdream.min.css" href="#">
                xDream
              </a>
            </div>
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-xpro rounded-1" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xpro.min.css" href="#">
                xPro
              </a>
            </div>
            <div class="col-4 mb-1">
              <a class="d-block py-3 text-white fs-xs fw-semibold bg-xplay rounded-1" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xplay.min.css" href="#">
                xPlay
              </a>
            </div>
          </div>
          <!-- END Color Themes -->
        </div>
      </div>
      <!-- END Options -->
    </div>
    <!-- END Right Section -->
  </div>
  <!-- END Header Content -->

  <!-- Header Search -->
  <div id="page-header-search" class="overlay-header bg-sidebar-dark">
    <div class="content-header">
      <form class="w-100" action="be_pages_generic_search.php" method="POST">
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
  <div id="page-header-loader" class="overlay-header bg-sidebar-dark">
    <div class="content-header">
      <div class="w-100 text-center">
        <i class="fa fa-fw fa-2x fa-sun fa-spin text-white"></i>
      </div>
    </div>
  </div>
  <!-- END Header Loader -->
</header>
<!-- END Header -->
