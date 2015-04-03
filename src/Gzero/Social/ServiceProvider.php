<?php namespace Gzero\Social;

use Gzero\OAuth\LaravelSession;
use Gzero\OAuth\OAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as SP;

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
class ServiceProvider extends SP {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->package('gzero/social', 'gzero-social');
        $this->addLinksToUserMenu();
        $this->bind();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // ...
    }

    /**
     * Add additional file to store routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        require_once __DIR__ . '/../../routes.php';
    }

    /**
     * Bind additional classes
     *
     * @return void
     */
    private function bind()
    {
        $this->app['oauth'] = $this->app->share(
            function ($app) {
                return new OAuth(
                    $this->app['config']->get('gzero-social::services'), // cfg
                    new LaravelSession($this->app->make('session')) // session
                );
            }
        );
    }

    /**
     * Add additional links to user account menu
     */
    public function addLinksToUserMenu()
    {
        $this->app['user.menu']->addLink(\URL::route('connectedServices'), \Lang::get('gzero-social::common.connectedServices'));
    }
}
