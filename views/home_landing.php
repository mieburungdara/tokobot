<?php
// The global $dm object is already loaded from public/index.php
global $dm;

// Muat konfigurasi spesifik untuk halaman landing
require_once ROOT_PATH . '/views/inc/get_started/landing/config.php';

// Render semua elemen halaman
require_once VIEWS_PATH . '/inc/_global/views/head_start.php';
require_once VIEWS_PATH . '/inc/_global/views/head_end.php';
require_once VIEWS_PATH . '/inc/_global/views/page_start.php';
?>

<!-- Hero -->
<div class="hero hero-lg bg-body-extra-light overflow-hidden">
  <div class="hero-inner">
    <div class="content content-full">
      <div class="row">
        <div class="col-lg-5 text-center text-lg-start d-lg-flex align-items-lg-center">
          <div>
            <h1 class="h2 fw-bold mb-3">
              Tokobot
            </h1>
            <p class="fs-4 text-muted mb-5">
              Automate your Telegram tasks with ease.
            </p>
            <div>
              <a class="btn btn-primary px-3 py-2 m-1" href="/dashboard">
                <i class="fa fa-fw fa-arrow-right opacity-50 me-1"></i> Get Started
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 offset-lg-1 d-none d-lg-block">
          <img class="img-fluid rounded" src="/assets/media/various/promo_dashboard.png" srcset="/assets/media/various/promo_dashboard@2x.png 2x"  alt="Hero Promo">
        </div>
      </div>
    </div>
  </div>
  <div class="hero-meta">
    <div>
      <span class="d-inline-block animated bounce infinite">
        <i class="si si-arrow-down text-muted fa-2x"></i>
      </span>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Features Section -->
<div id="features" class="bg-body-light">
  <div class="content content-full">
    <div class="py-5 push">
      <h2 class="mb-2 text-center">Packed with Features</h2>
      <h3 class="text-muted mb-0 text-center">Everything you need to get your work done.</h3>
    </div>
    <div class="row items-push">
      <div class="col-md-4">
        <div class="block block-rounded text-center h-100 mb-0">
          <div class="block-content py-5"><i class="fa fa-2x fa-rocket text-primary"></i></div>
          <div class="block-content block-content-full bg-body-light"><h4 class="fw-semibold mb-0">Powerful</h4></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="block block-rounded text-center h-100 mb-0">
          <div class="block-content py-5"><i class="fa fa-2x fa-cogs text-primary"></i></div>
          <div class="block-content block-content-full bg-body-light"><h4 class="fw-semibold mb-0">Automated</h4></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="block block-rounded text-center h-100 mb-0">
          <div class="block-content py-5"><i class="fa fa-2x fa-heart text-primary"></i></div>
          <div class="block-content block-content-full bg-body-light"><h4 class="fw-semibold mb-0">Easy to Use</h4></div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END Features Section -->

<!-- Pricing Section -->
<div id="pricing" class="bg-body-extra-light">
  <div class="content content-full">
    <div class="py-5 push">
      <h2 class="mb-2 text-center">Simple Pricing</h2>
      <h3 class="text-muted mb-0 text-center">Choose the plan that fits your needs.</h3>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-6 col-xl-4">
        <div class="block block-rounded">
          <div class="block-header block-header-default text-center">
            <h3 class="block-title">Pro Plan</h3>
          </div>
          <div class="block-content text-center p-4">
            <div class="fs-1 fw-bold">$19</div>
            <div class="text-muted">per month</div>
          </div>
          <div class="block-content block-content-full bg-body-light text-center">
            <a href="#" class="btn btn-primary px-4">Sign Up</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END Pricing Section -->

<?php
require_once VIEWS_PATH . '/inc/_global/views/page_end.php';
require_once VIEWS_PATH . '/inc/_global/views/footer_start.php';
require_once VIEWS_PATH . '/inc/_global/views/footer_end.php';
?>