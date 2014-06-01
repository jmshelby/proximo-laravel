<?php namespace Proximo\Controllers\Frontend;

use App;
use Auth;
use View;
use Input;
use Redirect;
use Exception;

use Carbon\Carbon;

class AuthController extends \Proximo\GenePool\Controller\Frontend\Root {

	const GLOBAL_PASSWORD = 'password';


    public function __construct()
    {
\Log::info("inside...");

        $this->beforeFilter('@filterEnsureAuthed', array('only' => array(
            'getLogout',
            'getProfile',
        )));
        $this->beforeFilter('@filterEnsureGuest', array('only' => array(
            'getLogin',
            'postLogin',
        )));
    }

	public function filterEnsureAuthed($route, $request)
	{
        if (Auth::guest()) {
			return Redirect::guest('auth/login');
		}
	}

	public function filterEnsureGuest($route, $request)
	{
        if (Auth::check()) {
			return Redirect::to('/');
		}
	}

	protected function _ensureUsernameExists($username)
	{
		$existingUser = \User::whereUsername($username)->first();
		
		$hash = \Hash::make(self::GLOBAL_PASSWORD);

		if (!$existingUser) {
			$existingUser = new \User;
			$existingUser->username = $username;
		}

		$existingUser->password = $hash;
		$existingUser->save();
	}


	public function getLogin()
	{
		return View::make('user.login');
	}

	public function postLogin()
	{

       $userParams = array(
            'username' => Input::get('username'),
            'password' => Input::get('password')
        );

// Temporary Easy Auth
$this->_ensureUsernameExists($userParams['username']);
$userParams['password'] = self::GLOBAL_PASSWORD;

        if (Auth::attempt($userParams,true)) {

            $user = Auth::user();
            $user->increment('login_count');

            $login_events = $user->login_events;
            if (!is_array($login_events))
                $login_events = array();

            $login_events[] = array(
                'datetime' => Carbon::now(),
                'location' => 'frontend',
            );
            $user->login_events = $login_events;
            $user->save();

            return Redirect::intended('/')
                ->with('flash_notice', 'You are successfully logged in.');
        }

        // authentication failure! lets go back to the login page
        return Redirect::to('auth/login')
            ->with('flash_error', 'Your username/password combination was incorrect.')
            ->withInput();
	}

	public function getLogout()
	{
        Auth::logout();
        return Redirect::to('/')->with('flash_notice', 'You are successfully logged out.');
	}

	public function getProfile()
	{
		return View::make('user.profile');
	}

}
