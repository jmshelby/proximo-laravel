<?php namespace Proximo\Controllers\Frontend;

use \App;
use \View;
use \Input;
use \Redirect;
use \Exception;

use Proximo\Entities\Player;
use Proximo\Entities\Player\Transaction;

class IndexController extends \Proximo\GenePool\Controller\Frontend\Root
{

	public $service;

	public function __construct()
	{
		$this->service = App::make('proximo.service.frontend');
		$this->beforeFilter('@filterRequest');
	}

	public function _getUser()
	{
		return $this->service->getUser();
	}

	public function filterRequest($route, $request)
	{
		// Different from the regular auth filter, we'll
		//  call the frontend service, in case they need
		//  to check more things
		if (!$this->service->loggedInCheck())
			return Redirect::guest('auth/login');
	}

	public function getIndex()
	{
		$messages = $this->service->getUserMessages();
		return View::make('chat', array(
			'player' => $this->_getUser(),
			'username' => $this->_getUser()->username,
			'messages' => $messages,
		));
	}

	public function postMessage()
	{
		$message = Input::get('message');
		$lat = Input::get('latitude', null);
		$long = Input::get('longitude', null);
		$this->service->lastPosition($lat, $long);

		try {
			$this->service->userPostsMessage($message, $lat, $long);
		} catch (Exception $e) {
			$error = "Problem posting: ".$e->getMessage();
		}
		$redirect = Redirect::to('/');
		if (!empty($error)) {
			$notice = $error;
		} else {
			$notice = "You have successfully posted the message";
		}
		$redirect->with('flash_notice', $notice);
		return $redirect;
	}

}
