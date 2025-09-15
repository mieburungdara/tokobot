<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/backend/config.php'; ?>
<?php
// Page specific configuration
$dm->l_header_style = 'dark-glass';
?>
<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php'; ?>

<!-- Hero -->
<div class="bg-image" style="background-image: url('<?php echo $dm->assets_folder; ?>/media/photos/photo9@2x.jpg');">
  <div class="bg-black-50">
    <div class="content content-top content-full text-center">
      <h1 class="fw-bold text-white mt-5 mb-2">
        Check out our latest stories
      </h1>
      <h3 class="fw-normal text-white-75 mb-5">Be inspired and create something amazing today.</h3>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content content-full">
  <div class="row items-push">
    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(21, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">Get things done</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on March 30, 2024 · <em class="text-muted">9 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->

    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(13, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">What to do in the night market</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on March 24, 2024 · <em class="text-muted">8 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->

    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(23, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">Work &amp; Travel</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on March 21, 2024 · <em class="text-muted">14 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->

    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(24, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">Learn to code</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on March 18, 2024 · <em class="text-muted">9 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->

    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(4, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">Enjoy Life</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on March 16, 2024 · <em class="text-muted">17 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->

    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(6, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">Spring is here</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on March 14, 2024 · <em class="text-muted">13 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->

    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(7, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">Winter is coming</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on May 12, 2024 · <em class="text-muted">6 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->

    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(8, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">How to do a special sale</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on May 6, 2024 · <em class="text-muted">21 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->

    <!-- Story -->
    <div class="col-lg-4">
      <a class="block block-rounded block-link-pop h-100 mb-0" href="be_pages_blog_story.php">
        <?php $dm->get_photo(9, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="mb-1">Up in the mountains</h4>
          <p class="fs-sm">
            <span class="text-primary"><?php $dm->get_name(); ?></span> on May 1, 2024 · <em class="text-muted">20 min</em>
          </p>
          <p>
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet...
          </p>
        </div>
      </a>
    </div>
    <!-- END Story -->
  </div>

  <!-- Pagination -->
  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center push">
      <li class="page-item active">
        <a class="page-link" href="javascript:void(0)">1</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="javascript:void(0)">2</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="javascript:void(0)">3</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="javascript:void(0)">4</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="javascript:void(0)">5</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="javascript:void(0)" aria-label="Next">
          <span aria-hidden="true">
            <i class="fa fa-angle-right"></i>
          </span>
          <span class="visually-hidden">Next</span>
        </a>
      </li>
    </ul>
  </nav>
  <!-- END Pagination -->
</div>
<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>
<?php require 'inc/_global/views/footer_end.php'; ?>
