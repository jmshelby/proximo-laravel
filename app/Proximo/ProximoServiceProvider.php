<?php namespace Proximo;

use Proximo\Services\Frontend;
use Route;

class ProximoServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function boot()
    {

        // Routes

        Route::controller('proximo','\Proximo\Controllers\Frontend\IndexController',array(
            'getIndex'              => 'proximo.dashboard',
            'postMessage'           => 'proximo.postMessage',
            //'getAddHeart'           => 'proximo.addHeart',
            //'postChangePoolShare'   => 'proximo.changePoolShare',
            //'getGiveHeart'          => 'proximo.giveHeart',
            //'getTransactionHistory' => 'proximo.transactionHistory',
        ));

        //Route::controller('proximo-cron','\Proximo\Controllers\Frontend\CronController');

/*
        Route::controller('proximo\player','\Proximo\Controllers\Frontend\PlayerController',array(
            //'getIndex' => 'user.login',
        ));
*/

    }

    public function register()
    {
        $this->app->bindShared('proximo.manager', function($app)
        {
            return new ProximoManager();
        });
        $this->app->bindShared('proximo.service.frontend', function($app)
        {
            return new Frontend($app['proximo.manager'], $app['auth']);
        });
    }

}

