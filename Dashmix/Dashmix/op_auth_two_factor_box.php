<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php'; ?>

<!-- Page Content -->
<div class="row g-0 justify-content-center bg-body-dark">
  <div class="hero-static col-sm-10 col-md-8 col-xl-6 d-flex align-items-center p-2 px-sm-0">
    <!-- Two Factor Block -->
    <div class="block block-rounded block-transparent block-fx-pop w-100 mb-0 overflow-hidden bg-image" style="background-image: url('<?php echo $dm->assets_folder; ?>/media/photos/photo20@2x.jpg');">
      <div class="row g-0">
        <div class="col-lg-6 order-lg-1 bg-body-extra-light">
          <div class="block-content block-content-full px-lg-5 py-md-5 py-lg-6">
            <!-- Header -->
            <div class="mb-2 text-center">
              <a class="link-fx fw-bold fs-1" href="index.php">
                <span class="text-dark">Dash</span><span class="text-primary">mix</span>
              </a>
              <p class="text-uppercase fw-bold fs-sm text-muted">Two Factor Authentication</p>
              <p class="text-muted fs-sm">
                Please confirm your account by entering the authorization code sent to your mobile number *******9552.
              </p>
            </div>
            <!-- END Header -->

            <!-- Two Factor Form -->
            <form id="form-2fa" action="be_pages_auth_all.php" method="POST" class="text-center">
              <div class="d-flex items-center justify-content-center gap-2 mb-4">
                <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num1" name="num1" maxlength="1" style="width: 38px;">
                <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num2" name="num2" maxlength="1" style="width: 38px;">
                <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num3" name="num3" maxlength="1" style="width: 38px;">
                <span class="d-flex align-items-center">-</span>
                <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num4" name="num4" maxlength="1" style="width: 38px;">
                <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num5" name="num5" maxlength="1" style="width: 38px;">
                <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num6" name="num6" maxlength="1" style="width: 38px;">
              </div>
              <div>
                <button type="submit" class="btn btn-lg btn-hero btn-primary">
                  <i class="fa fa-fw fa-lock-open opacity-50 me-1"></i> Submit
                </button>
              </div>
              <p class="fs-sm py-4 text-muted mb-0">
                Haven't received it? <a href="javascript:void(0)">Resend a new code</a>
              </p>
            </form>
            <!-- END Two Factor Form -->
          </div>
        </div>
        <div class="col-lg-6 order-lg-0 bg-primary-dark-op d-flex align-items-center">
          <div class="block-content block-content-full px-lg-5 py-md-5 py-lg-6">
            <div class="d-flex">
              <a class="flex-shrink-0 img-link me-3" href="javascript:void(0)">
                <?php $dm->get_avatar(0, 'male', 64, true); ?>
              </a>
              <div class="flex-grow-1">
                <p class="text-white fw-semibold mb-1">
                  Amazing framework with tons of options! It helped us build our project!
                </p>
                <a class="text-white-75 fw-semibold" href="javascript:void(0)"><?php echo $dm->get_name('male'); ?>, Web Developer</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END Two Factor Block -->
  </div>
</div>
<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>

<!-- Page JS Code -->
<?php $dm->get_js('js/pages/op_auth_two_factor.min.js'); ?>

<?php require 'inc/_global/views/footer_end.php'; ?>