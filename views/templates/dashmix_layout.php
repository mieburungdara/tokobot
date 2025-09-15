<?php
/**
 * views/templates/dashmix_layout.php
 *
 * The main layout file for the Dashmix template.
 */

// The global $dm object is loaded in public/index.php
global $dm;

// Include Dashmix view files
require_once __DIR__ . '/../inc/_global/views/head_start.php';
require_once __DIR__ . '/../inc/_global/views/head_end.php';

// This file builds the page shell: #page-container, header, sidebar, and opens <main>
require_once __DIR__ . '/../inc/_global/views/page_start.php';
?>

<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
      <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3"><?php echo $dm->title; ?></h1>
      <?php if (isset($dm->breadcrumbs) && is_array($dm->breadcrumbs) && !empty($dm->breadcrumbs)) : ?>
      <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <?php foreach ($dm->breadcrumbs as $i => $breadcrumb) : ?>
            <?php if ($i < count($dm->breadcrumbs) - 1) : ?>
              <li class="breadcrumb-item">
                <a href="<?php echo isset($breadcrumb['url']) ? $breadcrumb['url'] : '#'; ?>"><?php echo $breadcrumb['name']; ?></a>
              </li>
            <?php else : ?>
              <li class="breadcrumb-item active" aria-current="page"><?php echo $breadcrumb['name']; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
      </nav>
      <?php endif; ?>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <?php
  // The controller renders the specific view into the $page_content variable
  if (isset($page_content)) {
    echo $page_content;
  }
  ?>
</div>
<!-- END Page Content -->

<?php
// This file closes the <main> and #page-container tags
require_once __DIR__ . '/../inc/_global/views/page_end.php';

// This includes the footer and closing </body> and </html> tags
require_once __DIR__ . '/../inc/_global/views/footer_start.php';
require_once __DIR__ . '/../inc/_global/views/footer_end.php';
?>