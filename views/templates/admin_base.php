<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom layout styles -->
    <link href="/public/css/custom_layout.css" rel="stylesheet">
    <!-- Custom styles from demo -->
    <link href="/public/css/styles.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container-fluid">
            <nav class="navbar navbar-dark bg-dark">
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="#"><h3>Admin Dashboard</h3></a>
            </nav>
        </div>
    </header>

    <div class="wrapper">
        <nav id="sidebarMenu" class="collapse d-lg-block sidebar collapse bg-dark">
            <div class="position-sticky">
                <h5 class="mb-3">Menu</h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/admin/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/admin/users"><i class="fas fa-users"></i> Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/admin/reports"><i class="fas fa-chart-line"></i> Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/admin/settings"><i class="fas fa-cog"></i> Settings</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="content">
            <?php require_once $contentView; ?>
        </main>
    </div>

    <footer class="bg-light text-center p-3 mt-auto">
        <div class="container-fluid">
            <p class="mb-0">&copy; 2023 Admin Area</p>
        </div>
    </footer>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>