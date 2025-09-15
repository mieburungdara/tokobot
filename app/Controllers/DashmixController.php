<?php

namespace TokoBot\Controllers;

class DashmixController extends BaseController
{
    protected function renderDashmix($viewPath, $pageTitle = 'Dashboard', $pageDescription = '', $mainNav = [], $breadcrumbs = [])
    {
        // Access the global $dm object, which is now created in public/index.php
        global $dm;

        // Secara otomatis mengatur link aktif berdasarkan URL saat ini
        $dm->main_nav_active = strtok($_SERVER['REQUEST_URI'], '?');

        $dm->title = $pageTitle;
        $dm->description = $pageDescription;
        $dm->breadcrumbs = $breadcrumbs;

        // --- START: Logika Filter Navigasi Berdasarkan Peran ---

        // 1. Ambil peran pengguna dari Session, untuk contoh kita beri default 'admin'
        $userRole = \TokoBot\Helpers\Session::get('user_role', 'admin');

        // 2. Filter navigasi utama berdasarkan peran pengguna
        $filteredNav = $this->filterNavByRole($dm->main_nav, $userRole);

        // --- END: Logika Filter ---

        // Gunakan navigasi kustom jika diberikan, jika tidak, gunakan yang sudah difilter
        if (!empty($mainNav)) {
            $dm->main_nav = $this->filterNavByRole($mainNav, $userRole);
        } else {
            $dm->main_nav = $filteredNav;
        }

        ob_start();
        require_once $viewPath;
        $page_content = ob_get_clean();

        // Gunakan konstanta path yang sudah kita definisikan
        require_once VIEWS_PATH . '/templates/dashmix_layout.php';
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
            // Tampilkan item jika tidak memiliki kunci 'roles' (publik),
            // atau jika peran pengguna ada di dalam array 'roles'.
            if (!isset($item['roles']) || in_array($userRole, $item['roles'])) {
                // Jika item punya submenu, filter submenu tersebut secara rekursif.
                if (isset($item['sub']) && is_array($item['sub'])) {
                    $item['sub'] = $this->filterNavByRole($item['sub'], $userRole);
                }
                $filtered[] = $item;
            }
        }
        return $filtered;
    }
}
