<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/backend/config.php'; ?>
<?php require 'inc/_global/views/head_start.php'; ?>

<!-- Page JS Plugins CSS -->
<?php $dm->get_css('js/plugins/simplemde/simplemde.min.css'); ?>

<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php'; ?>

<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
      <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Form Editors</h1>
      <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Forms</li>
          <li class="breadcrumb-item active" aria-current="page">Editors</li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- SimpleMDE Editor (js-simplemde class is initialized in Helpers.jsSimpleMDE()) -->
  <!-- For more info and examples you can check out https://github.com/NextStepWebs/simplemde-markdown-editor -->
  <h2 class="content-heading">SimpleMDE</h2>
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Markdown Editor</h3>
      <div class="block-options">
        <button type="button" class="btn-block-option">
          <i class="si si-settings"></i>
        </button>
      </div>
    </div>
    <div class="block-content">
      <form action="be_forms_editors.php" method="POST" onsubmit="return false;">
        <div class="mb-4">
          <!-- SimpleMDE Container -->
          <textarea class="js-simplemde" id="simplemde" name="simplemde">Hello SimpleMDE!</textarea>
        </div>
      </form>
    </div>
  </div>
  <!-- END SimpleMDE Editor -->
</div>
<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>

<!-- Page JS Plugins -->
<?php $dm->get_js('js/plugins/simplemde/simplemde.min.js'); ?>

<!-- Page JS Helpers (SimpleMDE plugins) -->
<script>Dashmix.helpersOnLoad(['js-simplemde']);</script>

<?php require 'inc/_global/views/footer_end.php'; ?>
