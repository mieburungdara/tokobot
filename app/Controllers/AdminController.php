<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Logger;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use TokoBot\Exceptions\DatabaseException;
use Symfony\Component\Process\Process;

class AdminController extends DashmixController
{
    public function index()
    {
        $this->dashmixDashboard();
    }

    public function dashmixDashboard()
    {
        // Widget Data: Bot Status
        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT count(*) FROM tbots");
        $totalBots = $stmt->fetchColumn();

        $botsFile = CONFIG_PATH . '/tbots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];
        $botsWithToken = count($botTokens);

        // Widget Data: Critical Logs
        $logFile = ROOT_PATH . '/logs/critical.log';
        $criticalLogs = [];
        if (file_exists($logFile)) {
            $fileContent = file_get_contents($logFile);
            $lines = explode("\n", trim($fileContent));
            $criticalLogs = array_slice(array_reverse($lines), 0, 5); // Get latest 5 lines
        }

        $viewData = [
            'totalBots' => $totalBots,
            'botsWithToken' => $botsWithToken,
            'criticalLogs' => $criticalLogs
        ];

        $breadcrumbs = [
            ['name' => 'Dashboard']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/dashboard.php',
            'Admin Dashboard',
            'Welcome to the admin dashboard.',
            [],
            $breadcrumbs,
            $viewData
        );
    }

    public function users()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Users']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();
        // Fetch users with their role names
        $stmt = $pdo->query("SELECT u.telegram_id, u.username, u.first_name, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.first_name ASC");
        $users = $stmt->fetchAll();

        // Fetch all available roles
        $stmtRoles = $pdo->query("SELECT id, name FROM roles ORDER BY name ASC");
        $roles = $stmtRoles->fetchAll();

        $this->renderDashmix(
            VIEWS_PATH . '/admin/users.php',
            'User Management',
            'Manage all application users.',
            [],
            $breadcrumbs,
            ['users' => $users, 'roles' => $roles]
        );
    }

    public function settings()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Settings']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/settings.php',
            'Application Settings',
            'Configure application settings.',
            [],
            $breadcrumbs
        );
    }

    public function reports()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Reports']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/reports.php',
            'Reports',
            'View application reports.',
            [],
            $breadcrumbs
        );
    }

    public function botAnalytics()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Bot Analytics']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();

        // Command Usage
        $commandUsageStmt = $pdo->query("SELECT text, count(*) as command_count FROM messages WHERE text LIKE '/%' GROUP BY text ORDER BY command_count DESC LIMIT 10");
        $commandUsage = $commandUsageStmt->fetchAll();

        // Active Users
        $activeUsersStmt = $pdo->query("SELECT username, first_name, last_activity_at FROM users WHERE last_activity_at IS NOT NULL ORDER BY last_activity_at DESC LIMIT 10");
        $activeUsers = $activeUsersStmt->fetchAll();

        // Bot Errors
        $appLogFile = ROOT_PATH . '/logs/app.log';
        $telegramLogFile = ROOT_PATH . '/logs/telegram.log';
        $appLogs = file_exists($appLogFile) ? array_slice(array_reverse(explode("\n", trim(file_get_contents($appLogFile)))), 0, 5) : [];
        $telegramLogs = file_exists($telegramLogFile) ? array_slice(array_reverse(explode("\n", trim(file_get_contents($telegramLogFile)))), 0, 5) : [];

        $viewData = [
            'commandUsage' => $commandUsage,
            'activeUsers' => $activeUsers,
            'appLogs' => $appLogs,
            'telegramLogs' => $telegramLogs,
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/analytics.php',
            'Bot Analytics',
            'Analyze bot usage and activity.',
            [],
            $breadcrumbs,
            $viewData
        );
    }

    public function viewLogs()
    {
        $logChannel = $_GET['log'] ?? 'app';
        $allowedLogs = ['app', 'telegram', 'critical', 'tbot_error','auth','database'];

        if (!in_array($logChannel, $allowedLogs)) {
            $logChannel = 'app';
        }

        if ($logChannel === 'tbot_error') {
            $logFile = PUBLIC_PATH . '/tbot/error_log.txt';
        } else {
            $logFile = ROOT_PATH . "/logs/{$logChannel}.log";
        }

        if (isset($_GET['action']) && $_GET['action'] === 'clear') {
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }
            header("Location: /logs?log={$logChannel}");
            exit;
        }

        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Log Viewer']
        ];

        $logs = [];

        if (file_exists($logFile)) {
            $fileContent = file_get_contents($logFile);
            $logs = explode("\n", trim($fileContent));
            $logs = array_reverse($logs);
        }

        $this->renderDashmix(
            VIEWS_PATH . '/admin/logs.php',
            'Log Viewer',
            'View application logs.',
            [],
            $breadcrumbs,
            [
                'logs' => $logs,
                'logChannel' => $logChannel,
                'allowedLogs' => $allowedLogs,
            ]
        );
    }

    public function manageBots()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Bot Management']
        ];

        // Load bots from the database
        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT id, username, first_name, token IS NOT NULL as has_token FROM tbots ORDER BY first_name ASC");
        $tbots = $stmt->fetchAll();

        $this->renderDashmix(
            VIEWS_PATH . '/admin/tbots.php',
            'Bot Management',
            'Manage your Telegram bots.',
            [],
            $breadcrumbs,
            ['bots' => $tbots] // Pass bots data to the view
        );
    }

    public function storageChannels()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT id, bot_id, channel_id, last_used_at FROM bot_storage_channels ORDER BY bot_id ASC, id ASC");
        $storageChannels = $stmt->fetchAll();

        $this->renderDashmix(
            VIEWS_PATH . '/admin/storage_channels.php',
            'Storage Channel Management',
            'Manage Telegram storage channels for bots.',
            [],
            $breadcrumbs,
            ['storageChannels' => $storageChannels]
        );
    }

    public function addStorageChannel()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels', 'url' => '/storage-channels'],
            ['name' => 'Add']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT id, username FROM tbots ORDER BY username ASC");
        $bots = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $botId = $_POST['bot_id'] ?? null;
            $channelId = $_POST['channel_id'] ?? null;

            if (empty($botId) || empty($channelId)) {
                \TokoBot\Helpers\Session::flash('error_message', 'Bot ID and Channel ID cannot be empty.');
                header('Location: /storage-channels/add');
                exit();
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO bot_storage_channels (bot_id, channel_id) VALUES (?, ?)");
                $stmt->execute([$botId, $channelId]);
                \TokoBot\Helpers\Session::flash('success_message', 'Storage channel added successfully!');
            } catch (\PDOException $e) {
                \TokoBot\Helpers\Session::flash('error_message', 'Failed to add storage channel: ' . $e->getMessage());
            }

            header('Location: /storage-channels');
            exit();
        }

        $this->renderDashmix(
            VIEWS_PATH . '/admin/storage_channel_form.php',
            'Add Storage Channel',
            'Add a new Telegram storage channel.',
            [],
            $breadcrumbs,
            ['bots' => $bots]
        );
    }

    public function editStorageChannel($id)
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels', 'url' => '/storage-channels'],
            ['name' => 'Edit']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->prepare("SELECT id, bot_id, channel_id FROM bot_storage_channels WHERE id = ?");
        $stmt->execute([$id]);
        $channel = $stmt->fetch();

        if (!$channel) {
            \TokoBot\Helpers\Session::flash('error_message', 'Storage channel not found.');
            header('Location: /storage-channels');
            exit();
        }

        $stmt = $pdo->query("SELECT id, username FROM tbots ORDER BY username ASC");
        $bots = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $botId = $_POST['bot_id'] ?? null;
            $channelId = $_POST['channel_id'] ?? null;

            if (empty($botId) || empty($channelId)) {
                \TokoBot\Helpers\Session::flash('error_message', 'Bot ID and Channel ID cannot be empty.');
                header('Location: /storage-channels/edit/' . $id);
                exit();
            }

            try {
                $stmt = $pdo->prepare("UPDATE bot_storage_channels SET bot_id = ?, channel_id = ? WHERE id = ?");
                $stmt->execute([$botId, $channelId, $id]);
                \TokoBot\Helpers\Session::flash('success_message', 'Storage channel updated successfully!');
            } catch (\PDOException $e) {
                \TokoBot\Helpers\Session::flash('error_message', 'Failed to update storage channel: ' . $e->getMessage());
            }

            header('Location: /storage-channels');
            exit();
        }

        $this->renderDashmix(
            VIEWS_PATH . '/admin/storage_channel_form.php',
            'Edit Storage Channel',
            'Edit an existing Telegram storage channel.',
            [],
            $breadcrumbs,
            ['channel' => $channel, 'bots' => $bots, 'formAction' => '/storage-channels/edit/' . $id]
        );
    }

    public function deleteStorageChannel($id)
    {
        $pdo = \TokoBot\Helpers\Database::getInstance();
        try {
            $stmt = $pdo->prepare("DELETE FROM bot_storage_channels WHERE id = ?");
            $stmt->execute([$id]);
            \TokoBot\Helpers\Session::flash('success_message', 'Storage channel deleted successfully!');
        } catch (\PDOException $e) {
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to delete storage channel: ' . $e->getMessage());
        }

        header('Location: /storage-channels');
        exit();
    }

    public function updateUserRole()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit();
        }

        $telegramId = $_POST['telegram_id'] ?? null;
        $roleId = $_POST['role_id'] ?? null;

        if (empty($telegramId) || empty($roleId)) {
            \TokoBot\Helpers\Session::flash('error_message', 'User ID or Role ID is missing.');
            header('Location: /users');
            exit();
        }

        $pdo = \TokoBot\Helpers\Database::getInstance();

        try {
            $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE telegram_id = ?");
            $stmt->execute([$roleId, $telegramId]);
            \TokoBot\Helpers\Session::flash('success_message', 'User role updated successfully!');
        } catch (\PDOException $e) {
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to update user role: ' . $e->getMessage());
        }

        header('Location: /users');
        exit();
    }
}
