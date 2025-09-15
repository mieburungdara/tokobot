<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php'; ?>

<!-- Page Content -->
<div class="bg-image" style="background-image: url('<?php echo $dm->assets_folder; ?>/media/photos/photo19@2x.jpg');">
  <div class="row g-0 justify-content-center bg-primary-dark-op">
    <div class="hero-static col-sm-8 col-md-6 col-xl-4 d-flex align-items-center p-2 px-sm-0">
      <!-- Two Factor Block -->
      <div class="block block-transparent block-rounded w-100 mb-0 overflow-hidden">
        <div class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-body-extra-light">
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
              <input type="text" class="form-control form-control-lg text-center px-0" id="num1" name="num1" maxlength="1" style="width: 38px;">
              <input type="text" class="form-control form-control-lg text-center px-0" id="num2" name="num2" maxlength="1" style="width: 38px;">
              <input type="text" class="form-control form-control-lg text-center px-0" id="num3" name="num3" maxlength="1" style="width: 38px;">
              <span class="d-flex align-items-center">-</span>
              <input type="text" class="form-control form-control-lg text-center px-0" id="num4" name="num4" maxlength="1" style="width: 38px;">
              <input type="text" class="form-control form-control-lg text-center px-0" id="num5" name="num5" maxlength="1" style="width: 38px;">
              <input type="text" class="form-control form-control-lg text-center px-0" id="num6" name="num6" maxlength="1" style="width: 38px;">
            </div>
            <div>
              <button type="submit" class="btn btn-lg btn-hero btn-primary mb-4">
                <i class="fa fa-fw fa-lock-open opacity-50 me-1"></i> Submit
              </button>
            </div>
            <p class="fs-sm text-muted mb-0">
              Haven't received it? <a href="javascript:void(0)">Resend a new code</a>
            </p>
          </form>
          <!-- END Two Factor Form -->
        </div>
        <div class="block-content bg-body">
          <div class="d-flex justify-content-center text-center push">
            <a class="item item-circle item-tiny me-1 bg-default" data-toggle="theme" data-theme="default" href="#"></a>
            <a class="item item-circle item-tiny me-1 bg-xwork" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xwork.min.css" href="#"></a>
            <a class="item item-circle item-tiny me-1 bg-xmodern" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xmodern.min.css" href="#"></a>
            <a class="item item-circle item-tiny me-1 bg-xeco" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xeco.min.css" href="#"></a>
            <a class="item item-circle item-tiny me-1 bg-xsmooth" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xsmooth.min.css" href="#"></a>
            <a class="item item-circle item-tiny me-1 bg-xinspire" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xinspire.min.css" href="#"></a>
            <a class="item item-circle item-tiny me-1 bg-xdream" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xdream.min.css" href="#"></a>
            <a class="item item-circle item-tiny me-1 bg-xpro" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xpro.min.css" href="#"></a>
            <a class="item item-circle item-tiny bg-xplay" data-toggle="theme" data-theme="<?php echo $dm->assets_folder; ?>/css/themes/xplay.min.css" href="#"></a>
          </div>
        </div>
      </div>
      <!-- END Two Factor Block -->
    </div>
  </div>
</div>
<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>

<!-- Page JS Code -->
<?php $dm->get_js('js/pages/op_auth_two_factor.min.js'); ?>

<?php require 'inc/_global/views/footer_end.php'; ?>