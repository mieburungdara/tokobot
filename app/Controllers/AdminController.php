<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Logger;
use TokoBot\Helpers\Session;
use TokoBot\Models\Bot;
use TokoBot\Models\MessageModel;
use TokoBot\Models\RoleModel;
use TokoBot\Models\StorageChannelModel;
use TokoBot\Models\UserModel;

use Psr
use TokoBot\Helpers\CacheKeyManager;
use Psr\SimpleCache\CacheInterface;
use TokoBot\Core\Container;

class AdminController extends DashmixController
{
    private CacheInterface $cache;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->cache = $container->get(CacheInterface::class);
    }

    public function index()
    {
        $this->dashmixDashboard();
    }

    public function dashmixDashboard()
    {
        $cacheKey = CacheKeyManager::forDashboardStats();
        $viewData = $this->cache->get($cacheKey);

        if ($viewData === null) {
            // Widget Data: Bot Status
            $totalBots = Bot::countAll();

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

            // Store in cache for 60 seconds
            $this->cache->set($cacheKey, $viewData, 60);
        }

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

        $users = UserModel::getAllWithRoles();
        $roles = RoleModel::getAllSortedByName();

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

        // Bot Errors
        $appLogFile = ROOT_PATH . '/logs/app.log';
        $telegramLogFile = ROOT_PATH . '/logs/telegram.log';
        $appLogs = file_exists($appLogFile) ? array_slice(array_reverse(explode("\n", trim(file_get_contents($appLogFile)))), 0, 5) : [];
        $telegramLogs = file_exists($telegramLogFile) ? array_slice(array_reverse(explode("\n", trim(file_get_contents($telegramLogFile)))), 0, 5) : [];

        $viewData = [
            'commandUsage' => MessageModel::getCommandUsageStats(),
            'activeUsers' => UserModel::getRecentlyActiveUsers(),
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

        $this->renderDashmix(
            VIEWS_PATH . '/admin/tbots.php',
            'Bot Management',
            'Manage your Telegram bots.',
            [],
            $breadcrumbs,
            ['bots' => Bot::findAllWithTokenStatus()] // Pass bots data to the view
        );
    }

    public function storageChannels()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/storage_channels.php',
            'Storage Channel Management',
            'Manage Telegram storage channels for bots.',
            [],
            $breadcrumbs,
            ['storageChannels' => StorageChannelModel::getAllSorted()]
        );
    }

    public function addStorageChannel()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels', 'url' => '/storage-channels'],
            ['name' => 'Add']
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $botId = $_POST['bot_id'] ?? null;
            $channelId = $_POST['channel_id'] ?? null;

            if (empty($botId) || empty($channelId)) {
                Session::flash('error_message', 'Bot ID and Channel ID cannot be empty.');
                header('Location: /storage-channels/add');
                exit();
            }

            try {
                (new StorageChannelModel())->create(['bot_id' => $botId, 'channel_id' => $channelId]);
                Session::flash('success_message', 'Storage channel added successfully!');
            } catch (\Exception $e) {
                Session::flash('error_message', 'Failed to add storage channel: ' . $e->getMessage());
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
            ['bots' => Bot::findAllForSelection()]
        );
    }

    public function editStorageChannel($id)
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels', 'url' => '/storage-channels'],
            ['name' => 'Edit']
        ];

        $storageChannelModel = new StorageChannelModel();
        $channel = $storageChannelModel->find($id);

        if (!$channel) {
            Session::flash('error_message', 'Storage channel not found.');
            header('Location: /storage-channels');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $botId = $_POST['bot_id'] ?? null;
            $channelId = $_POST['channel_id'] ?? null;

            if (empty($botId) || empty($channelId)) {
                Session::flash('error_message', 'Bot ID and Channel ID cannot be empty.');
                header('Location: /storage-channels/edit/' . $id);
                exit();
            }

            try {
                $storageChannelModel->update($id, ['bot_id' => $botId, 'channel_id' => $channelId]);
                Session::flash('success_message', 'Storage channel updated successfully!');
            } catch (\Exception $e) {
                Session::flash('error_message', 'Failed to update storage channel: ' . $e->getMessage());
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
            ['channel' => $channel, 'bots' => Bot::findAllForSelection(), 'formAction' => '/storage-channels/edit/' . $id]
        );
    }

    public function deleteStorageChannel($id)
    {
        try {
            (new StorageChannelModel())->delete($id);
            Session::flash('success_message', 'Storage channel deleted successfully!');
        } catch (\Exception $e) {
            Session::flash('error_message', 'Failed to delete storage channel: ' . $e->getMessage());
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
            Session::flash('error_message', 'User ID or Role ID is missing.');
            header('Location: /users');
            exit();
        }

        try {
            UserModel::updateRoleByTelegramId($telegramId, $roleId);
            Session::flash('success_message', 'User role updated successfully!');
        } catch (\Exception $e) {
            Session::flash('error_message', 'Failed to update user role: ' . $e->getMessage());
        }

        header('Location: /users');
        exit();
    }

    public function cacheManagement()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Cache Management']
        ];

        $viewData = [
            'apcu_enabled' => extension_loaded('apcu') && apcu_enabled(),
            'cache_info' => null,
            'sma_info' => null
        ];

        if (isset($_GET['action']) && $_GET['action'] === 'clear') {
            if ($this->cache->clear()) {
                Session::flash('success_message', 'APCu cache has been cleared successfully!');
            } else {
                Session::flash('error_message', 'Failed to clear APCu cache.');
            }
            header('Location: /admin/cache');
            exit();
        }

        if ($viewData['apcu_enabled']) {
            $viewData['cache_info'] = apcu_cache_info();
            $viewData['sma_info'] = apcu_sma_info();
        }

        $this->renderDashmix(
            VIEWS_PATH . '/admin/cache_management.php',
            'Cache Management',
            'Monitor and manage the application cache (APCu).',
            [],
            $breadcrumbs,
            $viewData
        );
    }
}
