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

        // Routes
        Route::controller('auth',
			'Proximo\Controllers\Frontend\AuthController');

        Route::controller('ajax/message',
			'Proximo\Controllers\Frontend\Ajax\MessageController');

        Route::controller('webservice/message',
			'Proximo\Controllers\Webservice\MessageController');

        Route::controller('/',
			'Proximo\Controllers\Frontend\IndexController');

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

