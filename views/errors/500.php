<?php
global $dm;
require_once VIEWS_PATH . '/inc/_global/views/head_start.php';
require_once VIEWS_PATH . '/inc/_global/views/head_end.php';
require_once VIEWS_PATH . '/inc/_global/views/page_start.php';
?>
<div class="bg-image" style="background-image: url('/assets/media/photos/photo20@2x.jpg');">
  <div class="row g-0 justify-content-end bg-xwork-op">
    <div class="hero-static col-md-5 d-flex flex-column bg-body-extra-light">
      <div class="flex-grow-0 p-5">
        <a class="link-fx fw-bold fs-2" href="/">
          <span class="text-dark">Toko</span><span class="text-primary">Bot</span>
        </a>
      </div>
      <div class="flex-grow-1 d-flex align-items-center p-5 bg-body-light">
        <div class="w-100">
          <p class="text-danger fs-4 fw-bold text-uppercase mb-2">500 Error</p>
          <h1 class="fw-bold mb-2">Internal Server Error</h1>
          <p class="fs-4 fw-medium text-muted mb-5">There was an issue with our server and couldn’t fulfill your request.</p>
          <a class="btn btn-lg btn-alt-danger" href="/"><i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Home</a>
        </div>
      </div>
      <div class="flex-grow-0 p-5"><p class="fs-sm fw-medium text-muted mb-0">Copyright &copy; <span data-toggle="year-copy"></span></p></div>
    </div>
  </div>
</div>
<?php
require_once VIEWS_PATH . '/inc/_global/views/page_end.php';
require_once VIEWS_PATH . '/inc/_global/views/footer_start.php';
require_once VIEWS_PATH . '/inc/_global/views/footer_end.php';
?>