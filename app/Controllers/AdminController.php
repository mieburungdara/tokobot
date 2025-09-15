<?php

namespace TokoBot\Controllers;

class AdminController extends DashmixController
{
    public function index()
    {
        $this->dashmixDashboard();
    }

    public function dashmixDashboard()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/dashboard.php',
            'Admin Dashboard',
            'Welcome to the admin dashboard.',
            [],
            $breadcrumbs
        );
    }

    public function users()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Users']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/users.php',
            'User Management',
            'Manage all application users.',
            [],
            $breadcrumbs
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

    public function manageBots()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Bot Management']
        ];

        // Load bots from the database
        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM bots ORDER BY first_name ASC");
        $botsFromDb = $stmt->fetchAll();

        // Load tokens from the config file
        $botsFile = CONFIG_PATH . '/bots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];

        // Check token status for each bot
        $bots = array_map(function ($bot) use ($botTokens) {
            $bot['has_token'] = isset($botTokens[$bot['id']]);
            return $bot;
        }, $botsFromDb);

        $this->renderDashmix(
            VIEWS_PATH . '/admin/bots.php',
            'Bot Management',
            'Manage your Telegram bots.',
            [],
            $breadcrumbs,
            ['bots' => $bots] // Pass bots data to the view
        );
    }

    public function addBot()
    {
        $token = $_POST['token'] ?? '';

        if (empty($token)) {
            \TokoBot\Helpers\Session::flash('error_message', 'Token cannot be empty.');
            header('Location: /tbot');
            exit();
        }

        try {
            // Use the provided token for this request
            new \TelegramBot\Telegram($token);
            $response = \TelegramBot\Request::getMe();

            if ($response->isOk()) {
                $botInfo = $response->getResult();
                $botId = $botInfo->getId();

                // --- Save public info to DB ---
                $pdo = \TokoBot\Helpers\Database::getInstance();
                $sql = "INSERT INTO bots (id, username, first_name, is_bot) VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE username = VALUES(username), first_name = VALUES(first_name)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $botId,
                    $botInfo->getUsername(),
                    $botInfo->getFirstName(),
                    $botInfo->getIsBot()
                ]);

                // --- Save token to config file ---
                $botsFile = CONFIG_PATH . '/bots.php';
                $botTokens = file_exists($botsFile) ? require $botsFile : [];
                $botTokens[$botId] = $token;

                $fileContent = "<?php\n\n// Bot token configuration file\n// Maps Bot ID to its secret token.\nreturn " . var_export($botTokens, true) . ";\n";
                file_put_contents($botsFile, $fileContent);

                \TokoBot\Helpers\Session::flash('success_message', 'Bot \"' . $botInfo->getFirstName() . '\" has been added/updated successfully!');
            } else {
                throw new \Exception('Invalid token: ' . $response->getDescription());
            }
        } catch (\Exception $e) {
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to add bot: ' . $e->getMessage());
        }

        header('Location: /tbot');
        exit();
    }

    public function deleteBot($id)
    {
        try {
            // --- Delete from DB ---
            $pdo = \TokoBot\Helpers\Database::getInstance();
            $stmt = $pdo->prepare("DELETE FROM bots WHERE id = ?");
            $stmt->execute([$id]);

            // --- Delete token from config file ---
            $botsFile = CONFIG_PATH . '/bots.php';
            $botTokens = file_exists($botsFile) ? require $botsFile : [];
            if (isset($botTokens[$id])) {
                unset($botTokens[$id]);
                $fileContent = "<?php\n\n// Bot token configuration file\n// Maps Bot ID to its secret token.\nreturn " . var_export($botTokens, true) . ";\n";
                file_put_contents($botsFile, $fileContent);
            }

            // --- Delete webhook file ---
            $webhookFile = PUBLIC_PATH . '/bots/' . $id . '.php';
            if (file_exists($webhookFile)) {
                unlink($webhookFile);
            }

            \TokoBot\Helpers\Session::flash('success_message', 'Bot has been deleted successfully!');
        } catch (\Exception $e) {
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to delete bot: ' . $e->getMessage());
        }

        header('Location: /tbot');
        exit();
    }
}