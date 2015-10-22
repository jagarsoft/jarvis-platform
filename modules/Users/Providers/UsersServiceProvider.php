<?php namespace Modules\Users\Providers;

use Modules\Users\Entities\Role;
use Modules\Users\Entities\User;
use Illuminate\Support\ServiceProvider;
use Modules\Users\Observers\RoleObserver;
use Modules\Users\Observers\UserObserver;

class UsersServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerTranslations();
        $this->registerViews();
        \Entrust::routeNeedsRoleOrPermission('users*', ['administrator'],
            ['user-create', 'user-edit', 'user-delete', 'user-activate'], null, false);
        \Entrust::routeNeedsRoleOrPermission('config/users*', ['administrator'],
            ['user-configuration'], null, false);
        \Entrust::routeNeedsRoleOrPermission('roles*', ['administrator'],
            ['create-role', 'edit-role', 'delete-role', 'admin-permissions'], null, false);
        User::observe(new UserObserver());
        Role::observe(new RoleObserver());
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            \Modules\Users\Console\CreateEntities::class,
            \Modules\Users\Console\GenerateAdmin::class,
            \Modules\Users\Console\GenerateDefaultRoleAndPerms::class,
        ]);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('users.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'users'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/users');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom([$viewPath, $sourcePath], 'users');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/users');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'users');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'users');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
