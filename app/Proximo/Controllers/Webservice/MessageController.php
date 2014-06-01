<?php namespace Proximo\Controllers\Webservice;

use \App;
use \View;
use \Input;
use \Redirect;
use \Exception;

class MessageController extends \Proximo\GenePool\Controller\Webservice\Root {

    public $service;

    public function __construct()
    {
        $this->service = App::make('proximo.service.api');
        //$this->beforeFilter('@filterRequest');
    }

	public function _getUser()
	{
		return $this->service->getUser();
	}


	public function getIndex()
	{
	}


/*
    public function filterRequest($route, $request)
    {
        // Different from the regular auth filter, we'll
        //  call the frontend service, in case they need
        //  to check more things
        if (!$this->service->loggedInCheck())
            return Redirect::guest(route('user.login'));
    }

	public function getIndex()
	{
		$messages = $this->service->getUserMessages();
		return View::make('proximo.dashboard', array(
			'player' => $this->_getUser(),
			'messages' => $messages,
		));
	}

	public function postMessage()
	{
		$message = Input::get('content');
		$lat = Input::get('latitude', 0);
		$long = Input::get('longitude', 0);

		try {
			$this->service->userPostsMessage($message, $lat, $long);
		} catch (Exception $e) {
			$error = "Problem posting: ".$e->getMessage();
		}
		$redirect = Redirect::route('proximo.dashboard');
		if (!empty($error)) {
			$notice = $error;
		} else {
			$notice = "You have successfully posted the message";
		}
		$redirect->with('flash_notice', $notice);
		return $redirect;
	}
*/

}
