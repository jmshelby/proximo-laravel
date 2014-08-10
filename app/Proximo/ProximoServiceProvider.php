<?php namespace Proximo;

use App;
use View;
use Route;
use Input;
use Session;

use Proximo\Services\Frontend as POVFrontend;
use Proximo\Services\Api as POVApi;

class ProximoServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function boot()
    {
    }

    public function register()
    {
        $this->app->bindShared('proximo.manager', function($app)
        {
            return new ProximoManager();
        });
        $this->app->bindShared('proximo.service.frontend', function($app)
        {
            return new POVFrontend($app['proximo.manager'], $app['auth']);
        });
        $this->app->bindShared('proximo.service.api', function($app)
        {
            return new POVApi($app['proximo.manager']);
        });
    }

}

