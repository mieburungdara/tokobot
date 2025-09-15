<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .wrapper {
            display: flex;
            flex: 1;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 15px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 0;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar li.active a {
            background-color: #007bff;
            color: white;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container-fluid">
            <h3>Admin Dashboard</h3>
        </div>
    </header>

    <div class="wrapper">
        <nav class="sidebar">
            <h4>Menu</h4>
            <ul class="list-unstyled">
                <?php
                $adminMenu = require __DIR__ . '/../../config/admin_menu.php';
                $currentUri = $_SERVER['REQUEST_URI'];
                foreach ($adminMenu as $menuItem) {
                    $isActive = (strpos($currentUri, $menuItem['url']) === 0) ? 'active' : '';
                    echo '<li class="' . $isActive . '"><a href="' . $menuItem['url'] . '">' . $menuItem['label'] . '</a></li>';
                }
                ?>
            </ul>
        </nav>
        <main class="content">
            <?php require_once $contentView; ?>
        </main>
    </div>

    <footer class="footer">
        <p>&copy; 2023 Admin Panel</p>
    </footer>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>