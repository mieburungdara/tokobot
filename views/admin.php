<div class="admin-container">
    <aside class="admin-sidebar">
        <h2>Admin Menu</h2>
        <ul>
            <li><a href="/admin/dashboard">Dashboard</a></li>
            <li><a href="/admin/users">User Management</a></li>
            <li><a href="/admin/settings">Settings</a></li>
            <li><a href="/admin/reports">Reports</a></li>
        </ul>
    </aside>
    <main class="admin-content">
        <h1>Admin Panel</h1>
        <p>Selamat datang di halaman panel admin. Gunakan menu di samping untuk navigasi.</p>
        <!-- Konten spesifik halaman admin akan dimuat di sini -->
    </main>
</div>

<style>
    .admin-container {
        display: flex;
        min-height: calc(100vh - 100px); /* Adjust based on header/footer height */
    }
    .admin-sidebar {
        width: 200px;
        background-color: #f0f0f0;
        padding: 20px;
        border-right: 1px solid #ddd;
    }
    .admin-sidebar ul {
        list-style: none;
        padding: 0;
    }
    .admin-sidebar li a {
        display: block;
        padding: 10px 0;
        text-decoration: none;
        color: #333;
    }
    .admin-sidebar li a:hover {
        background-color: #e0e0e0;
    }
    .admin-content {
        flex-grow: 1;
        padding: 20px;
    }
</style>
