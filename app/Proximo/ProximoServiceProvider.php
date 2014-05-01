<?php namespace Proximo;

use Proximo\Services\Frontend;
use App;
use View;
use Route;
use Input;
use Session;

class ProximoServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function boot()
    {


		Route::get('/', array('as' => 'root', function () {

			$man = App::make('proximo.manager');

			\Log::info("session::all: " . print_r(Session::all(),true));

			if (Session::has('proximo.id')) {

				$username = Session::get('proximo.id');

				//$lat = Input::get('latitude', Session::get('proximo.last_coords.lat', null));
				//$long = Input::get('longitude', Session::get('proximo.last_coords.long', null));
				$lat = Session::get('proximo.last_coords.lat', null);
				$long = Session::get('proximo.last_coords.long', null);

				$messages = null;
				if (!is_null($lat) && !is_null($long)) {
					$messages = $man->getMessagesNear($lat, $long);
				}

				return View::make('chat')
					->with('username',$username)
					->with('messages',$messages)
				;

			} else {

				return View::make('beginning');

			}


		}));


		Route::post('/login', array('as' => 'post.login', function () {

			$id = Input::get('handle');

			// TODO -- Should we Make sure the posted id is unique ???

			Session::put('proximo.id', $id);

			return \Redirect::route('root');

		}));

		Route::post('/postMessage', array('as' => 'post.postMessage', function () {

			$man = App::make('proximo.manager');

			$lat = Input::get('latitude', null);
			$long = Input::get('longitude', null);
			$message = Input::get('message', null);
			$session = Session::get('_token');

			Session::put('proximo.last_coords.lat', $lat);
			Session::put('proximo.last_coords.long', $long);

			$man->postMessage($session, $message, $lat, $long);

			return \Redirect::route('root')->withInput();

		}));


// Old Stuff ....
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

