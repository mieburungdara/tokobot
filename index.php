<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'admin':
        include 'admin.php';
        break;
    case 'dashboard':
        include 'dashboard.php';
        break;
    default:
        echo "<h1>Selamat Datang di Tokobot</h1>";
        echo '<a href="?page=admin">Panel Admin</a><br>';
        echo '<a href="?page=dashboard">Dasbor Member</a>';
        break;
}
?>