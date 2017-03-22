<?php namespace Gzero\Social;

use Gzero\Core\AbstractServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteServiceProvider;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class ServiceProvider
 *
 * @package    Gzero
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2014, Adrian Skierniewski
 */
class ServiceProvider extends AbstractServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * List of additional providers
     *
     * @var array
     */
    protected $providers = [
        SocialiteServiceProvider::class
    ];

    /**
     * List of service providers aliases
     *
     * @var array
     */
    protected $aliases = [
        Socialite::class,
    ];

    /**
     * Bootstrap the application events.
     * WARNING: Order of execution functions in boot is important, because we're using translations in our routes
     *
     * @return void
     */
    public function boot()
    {
        $viewPath        = __DIR__ . '/../../resources/views';
        $translationPath = __DIR__ . '/../../resources/lang';
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');
        $this->loadViewsFrom($viewPath, 'gzero-social');
        $this->loadTranslationsFrom($translationPath, 'gzero-social');
        $this->loadRoutesFrom(__DIR__ . '/../../../routes/routes.php');
        $this->addLinksToUserMenu();
        $this->publishes(
            [
                $viewPath => base_path('resources/views/vendor/gzero-social')
            ],
            'views'
        );
        $this->publishes(
            [
                $translationPath => base_path('resources/lang/vendor/gzero-social'),
            ],
            'lang'
        );
        $this->registerHelpers();
    }

    /**
     * Add additional links to user account menu
     */
    public function addLinksToUserMenu()
    {
        app('gzero.menu.account')->addLink(route('connectedServices'), trans('gzero-social::common.connected_services'));
    }

    /**
     * Add additional file to store helpers
     *
     * @return void
     */
    protected function registerHelpers()
    {
        require_once __DIR__ . '/helpers.php';
    }

}
