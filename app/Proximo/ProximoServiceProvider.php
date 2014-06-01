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


/*
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
*/


        // Routes


        Route::controller('auth',
			'Proximo\Controllers\Frontend\AuthController');


        Route::controller('/',
			'Proximo\Controllers\Frontend\IndexController');

        Route::controller('webservice/message',
			'Proximo\Controllers\Webservice\MessageController');


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

