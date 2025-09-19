<?php

namespace TokoBot\Controllers;

use TokoBot\Core\Container;
use TokoBot\Helpers\Auth;
use TokoBot\Services\AuthorizationService;

class DashmixController extends BaseController
{
    protected AuthorizationService $authService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->authService = $container->get(AuthorizationService::class);
    }

    protected function renderDashmix(
        $viewPath,
        $pageTitle = 'Dashboard',
        $pageDescription = '',
        $mainNav = [],
        $breadcrumbs = [],
        $data = [],
        $layoutPath = null // Tambahkan parameter opsional untuk layout kustom
    ) {
        $dm = $this->container->get('template');

        // Secara otomatis mengatur link aktif berdasarkan URL saat ini
        $dm->main_nav_active = strtok($_SERVER['REQUEST_URI'], '?');

        $dm->title = $pageTitle;
        $dm->description = $pageDescription;
        $dm->breadcrumbs = $breadcrumbs;

        // --- START: Logika Filter Navigasi Berdasarkan Peran ---

        // 1. Ambil peran pengguna dari Session, default ke 'guest' jika tidak ada untuk keamanan
        $userRole = \TokoBot\Helpers\Session::get('user_role', 'guest');

        // 2. Filter navigasi utama berdasarkan peran pengguna (admin, member, atau guest)
        $filteredNav = $this->filterNavByRole($dm->main_nav, $userRole);

        // --- END: Logika Filter ---

        // Gunakan navigasi kustom jika diberikan, jika tidak, gunakan yang sudah difilter
        if (!empty($mainNav)) {
            $dm->main_nav = $this->filterNavByRole($mainNav, $userRole);
        } else {
            $dm->main_nav = $filteredNav;
        }

        // Make data available to the view file
        extract($data);

        ob_start();
        require_once $viewPath;
        $page_content = ob_get_clean();

        // Gunakan layout kustom jika disediakan, jika tidak, gunakan layout default.
        $finalLayoutPath = $layoutPath ?? VIEWS_PATH . '/templates/dashmix_layout.php';
        require_once $finalLayoutPath;
    }

    /**
     * Memfilter array navigasi secara rekursif berdasarkan peran pengguna.
     *
     * @param array $nav Array navigasi yang akan difilter.
     * @param string $userRole Peran pengguna saat ini.
     * @return array Array navigasi yang sudah difilter.
     */
    private function filterNavByRole($nav, $userRole)
    {
        $filtered = [];
        foreach ($nav as $item) {
            // If the item has specific roles defined, check against them.
            // Otherwise, assume it's visible to any authenticated user.
            $isAllowed = isset($item['roles']) ? $this->authService->any($item['roles']) : Auth::check();

            if ($isAllowed) {
                // If the item has a submenu, filter it recursively as well.
                if (isset($item['sub']) && is_array($item['sub'])) {
                    $item['sub'] = $this->filterNavByRole($item['sub'], $userRole); // Pass userRole for consistency, though it's not used in the new logic
                }
                $filtered[] = $item;
            }
        }
        return $filtered;
    }
}
