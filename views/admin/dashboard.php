<?php
$page_title = "Admin Dashboard";
$page_description = "Welcome to the admin dashboard.";

ob_start();
?>
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
      <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3"><?php echo $page_title; ?></h1>
      <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Overview</h3>
    </div>
    <div class="block-content">
      <p>This is a sample admin dashboard page using Dashmix template.</p>
      <p>You can add your widgets and content here.</p>
    </div>
  </div>
</div>
<!-- END Page Content -->
<?php
$page_content = ob_get_clean();

require __DIR__ . '/../templates/dashmix_layout.php';
?>