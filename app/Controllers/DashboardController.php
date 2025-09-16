<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Session;

class DashboardController extends BaseController
{
    public function index()
    {
        // Ambil peran pengguna dari session, default ke 'member' jika tidak ada.
        $userRole = Session::get('user_role', 'member');

        // Arahkan ke controller yang sesuai berdasarkan peran.
        if ($userRole === 'admin') {
            // Panggil method dashboard dari AdminController
            $adminController = new AdminController($this->container);
            return $adminController->dashmixDashboard();
        } else {
            // Panggil method dashboard dari MemberController
            $memberController = new MemberController($this->container);
            return $memberController->dashmixDashboard();
        }
    }
}
