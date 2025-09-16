<?php
/**
 * error_layout.php
 *
 * A self-contained layout for displaying error pages without
 * dependencies on the main template object ($dm).
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>Error | TokoBot</title>

    <meta name="robots" content="noindex, nofollow">

    <!-- Icons -->
    <link rel="shortcut icon" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/media/favicons/favicon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/media/favicons/apple-touch-icon-180x180.png">

    <!-- Stylesheets -->
    <!-- Dashmix framework -->
    <link rel="stylesheet" id="css-main" href="/assets/css/dashmix.min.css">
</head>

<body>
    <div id="page-container">
        <!-- Main Container -->
        <main id="main-container">
            <?php echo $pageContent; // The specific error content will be injected here ?>
        </main>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->

    <!-- Dashmix Core JS -->
    <script src="/assets/js/dashmix.app.min.js"></script>
</body>
</html>
