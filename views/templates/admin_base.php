<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/admin/users">User Management</a></li>
                <li><a href="/admin/settings">Settings</a></li>
                <li><a href="/admin/reports">Reports</a></li>
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