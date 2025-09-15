<?php
/**
 * views/templates/dashmix_layout.php
 *
 * The main layout file for the Dashmix template.
 */

// The controller should initialize the $dm object.
// This provides fallback defaults.
global $dm;
if (!is_object($dm)) {
    $dm = new stdClass();
}

// Set layout variable for full-width content. The controller can override this.
if (!isset($dm->l_m_content)) {
    $dm->l_m_content = '';
}

// Include Dashmix framework files
require_once __DIR__ . '/../inc/_global/config.php';
require_once __DIR__ . '/../inc/backend/config.php';
require_once __DIR__ . '/../inc/_global/views/head_start.php';
require_once __DIR__ . '/../inc/_global/views/head_end.php';

// This file builds the page shell: #page-container, header, sidebar, and opens <main>
require_once __DIR__ . '/../inc/_global/views/page_start.php';
?>

<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
      <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Full Width Content</h1>
      <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Layout</li>
          <li class="breadcrumb-item">Content</li>
          <li class="breadcrumb-item active" aria-current="page">Full Width</li>
        </ol>
      </nav>
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