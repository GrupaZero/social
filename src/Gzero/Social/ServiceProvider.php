<?php namespace Gzero\Social;

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
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bind();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->app->register('Gzero\OAuth\OAuthServiceProvider');
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
        //$this->app->bind(
        //    'League\Fractal\Manager',
        //    function () {
        //        $manager = new Manager();
        //        $manager->setSerializer(new ArraySerializer());
        //        return $manager;
        //    }
        //);
    }
}
