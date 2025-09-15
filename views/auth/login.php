<?php global $dm; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Login - Tokobot</title>
    <link rel="stylesheet" id="css-main" href="/assets/css/dashmix.min.css">
  </head>
  <body>
    <div id="page-container">
      <main id="main-container">
        <div class="bg-image" style="background-image: url('/assets/media/photos/photo22@2x.jpg');">
          <div class="row g-0 bg-primary-op">
            <div class="hero-static col-md-6 d-flex align-items-center bg-body-extra-light">
              <div class="p-3 w-100">
                <div class="mb-3 text-center">
                  <a class="link-fx fw-bold fs-1" href="/">
                    <span class="text-dark">Toko</span><span class="text-primary">Bot</span>
                  </a>
                  <p class="text-uppercase fw-bold fs-sm text-muted">Admin Login</p>
                </div>
                <div class="row g-0 justify-content-center">
                  <div class="col-sm-8 col-xl-6">
                    <form action="/xoradmin" method="POST">
                      <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger text-center mb-3"><?php echo $error; ?></div>
                      <?php endif; ?>
                      <div class="py-3">
                        <div class="mb-4">
                          <input type="password" class="form-control form-control-lg form-control-alt" id="password" name="password" placeholder="Password" autofocus>
                        </div>
                      </div>
                      <div class="mb-4">
                        <button type="submit" class="btn w-100 btn-lg btn-hero btn-primary">
                          <i class="fa fa-fw fa-sign-in-alt opacity-50 me-1"></i> Sign In
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="hero-static col-md-6 d-none d-md-flex align-items-md-center justify-content-md-center text-md-center">
              <div class="p-3">
                <p class="display-4 fw-bold text-white mb-3">
                  Administrative Access
                </p>
                <p class="fs-lg fw-semibold text-white-75 mb-0">
                  Copyright &copy; <span data-toggle="year-copy"></span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    <script src="/assets/js/dashmix.app.min.js"></script>
  </body>
</html>