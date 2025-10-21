<?php

namespace Modules\Subdomain\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Superadmin package permissions
     *
     * @return array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'subdomain_module',
                'label' => __('subdomain::lang.subdomain_accounts'),
                'default' => false,
            ],
        ];
    }

    /**
     * Adds cms menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        if (auth()->user()->can('superadmin')) {
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) {
                    $menu->url(action([SubdomainController::class, 'index']), __('subdomain::lang.subdomain_accounts'),
                        ['icon' => 'fa fas fa-globe', 'active' => request()->segment(1) == 'subdomains'])->order(51);
                }
            );
        }
    }
}
