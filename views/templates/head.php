<?php
$pageTitle = isset($pageTitle) ? $pageTitle : 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="/css/styles.css">
    <?php
    /*
    $cssFiles = glob(__DIR__ . '/../../public/build/styles.*.css');
    foreach ($cssFiles as $cssFile) {
        echo '<link href="/build/' . basename($cssFile) . '" rel="stylesheet">';
    }
    $jsFiles = glob(__DIR__ . '/../../public/build/main.*.js');
    foreach ($jsFiles as $jsFile) {
        echo '<script src="/build/' . basename($jsFile) . '"></script>';
    } $jsFiles = glob(__DIR__ . '/../../public/build/styles.*.js');
    foreach ($jsFiles as $jsFile) {
        echo '<script src="/build/' . basename($jsFile) . '"></script>';
    } */
    ?>
</head>
<body>