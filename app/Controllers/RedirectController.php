<?php

namespace TokoBot\Controllers;

use TokoBot\Core\Routing\Route;

class RedirectController extends BaseController
{
    /**
     * This route acts as a gatekeeper, redirecting users based on their role.
     * The actual logic is in the DashboardRedirectMiddleware.
     * This controller method is just a dummy handler to satisfy the router.
     */
    #[Route('/dashboard', middleware: ['AuthMiddleware', 'DashboardRedirectMiddleware'])]
    public function dashboardRedirect()
    {
        // This method will likely never be called, as the middleware will redirect and exit.
        return;
    }
}
