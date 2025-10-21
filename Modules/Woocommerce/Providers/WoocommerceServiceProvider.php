<?php

namespace Modules\Woocommerce\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema; //  Added
use App\Business;
use App\Utils\ModuleUtil;
use Illuminate\Console\Scheduling\Schedule;

class WoocommerceServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        //  Only register schedule commands if safe to do so
        $this->registerScheduleCommands();

        //  Wrap view composer inside Schema check to prevent table query before migrations
        if (Schema::hasTable('system')) {
            View::composer('woocommerce::layouts.partials.sidebar', function ($view) {
                $module_util = new ModuleUtil();

                if (auth()->check() && auth()->user()->can('superadmin')) {
                    $__is_woo_enabled = $module_util->isModuleInstalled('Woocommerce');
                } elseif (auth()->check()) {
                    $business_id = session()->get('user.business_id');
                    $__is_woo_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'woocommerce_module', 'superadmin_package');
                } else {
                    $__is_woo_enabled = false;
                }

                $view->with(compact('__is_woo_enabled'));
            });
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->registerCommands();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('woocommerce.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'woocommerce'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/woocommerce');
        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/woocommerce';
        }, config('view.paths')), [$sourcePath]), 'woocommerce');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/woocommerce');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'woocommerce');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'woocommerce');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            \Modules\Woocommerce\Console\WooCommerceSyncOrder::class,
            \Modules\Woocommerce\Console\WoocommerceSyncProducts::class,
        ]);
    }

    /**
     * Register WooCommerce scheduled commands safely.
     *
     * @return void
     */
    public function registerScheduleCommands()
    {
        $env = config('app.env');

        //  Ensure required tables exist before running
        if (Schema::hasTable('system') && Schema::hasTable('business')) {
            $module_util = new ModuleUtil();
            $is_installed = $module_util->isModuleInstalled(config('woocommerce.name'));

            if ($env === 'live' && $is_installed) {
                $businesses = Business::whereNotNull('woocommerce_api_settings')->get();

                foreach ($businesses as $business) {
                    $api_settings = json_decode($business->woocommerce_api_settings);
                    if (!empty($api_settings->enable_auto_sync)) {
                        // Schedule WooCommerce sync commands
                        $this->app->booted(function () use ($business) {
                            $schedule = $this->app->make(Schedule::class);
                            $schedule->command('pos:WoocommerceSyncProducts ' . $business->id)->twiceDaily(1, 13);
                            $schedule->command('pos:WooCommerceSyncOrder ' . $business->id)->twiceDaily(1, 13);
                        });
                    }
                }
            }
        }
    }
}
