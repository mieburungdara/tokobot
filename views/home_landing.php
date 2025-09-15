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

<!-- Section 1 -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="py-5 push">
      <h2 class="mb-2 text-center">
        Feature 1
      </h2>
      <h3 class="text-muted mb-0 text-center">
        Powerful Automation
      </h3>
    </div>
    <div class="text-center">
      <p>
        Detailed description of the first amazing feature of your product.
      </p>
    </div>
  </div>
</div>
<!-- END Section 1 -->

<!-- Section 2 -->
<div class="bg-body-extra-light">
  <div class="content content-full">
    <div class="py-5 push">
      <h2 class="mb-2 text-center">
        Feature 2
      </h2>
      <h3 class="text-muted mb-0 text-center">
        Easy to Use
      </h3>
    </div>
    <div class="text-center">
      <p>
        Detailed description of the second amazing feature of your product.
      </p>
    </div>
  </div>
</div>
<!-- END Section 2 -->

<!-- Section 3 -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="py-5 push">
      <h2 class="mb-2 text-center">
        Feature 3
      </h2>
      <h3 class="text-muted mb-0 text-center">
        24/7 Support
      </h3>
    </div>
    <div class="text-center">
      <p>
        Detailed description of the third amazing feature of your product.
      </p>
    </div>
  </div>
</div>
<!-- END Section 3 -->

<?php
require_once VIEWS_PATH . '/inc/_global/views/page_end.php';
require_once VIEWS_PATH . '/inc/_global/views/footer_start.php';
require_once VIEWS_PATH . '/inc/_global/views/footer_end.php';
?>