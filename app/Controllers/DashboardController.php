<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Session;
use TokoBot\Helpers\Logger;

class DashboardController extends BaseController
{
    public function index()
    {
        // Ambil peran pengguna dari session, default ke 'member' jika tidak ada.
        $userRole = Session::get('user_role', 'member');
        Logger::channel('auth')->info('DashboardController: User role detected.', [
            'user_id' => Session::get('user_id'),
            'user_role' => $userRole,
            'auth_source' => Session::get('auth_source')
        ]);

        // Arahkan ke controller yang sesuai berdasarkan peran.
        if ($userRole === 'admin') {
            // Panggil method dashboard dari AdminController, dibangun oleh container
            $adminController = $this->container->build(AdminController::class);
            return $adminController->dashmixDashboard();
        } else {
            // Panggil method dashboard dari MemberController, dibangun oleh container
            $memberController = $this->container->build(MemberController::class);
            return $memberController->index();
        }
    }
}
