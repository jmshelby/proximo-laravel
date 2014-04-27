<?php namespace Proximo\Controllers\Frontend;

use \App;
use \View;
use \Input;
use \Redirect;
use \Exception;

use Proximo\Entities\Player;
use Proximo\Entities\Player\Transaction;

class IndexController extends \Proximo\GenePool\Controller\Frontend\Root {

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
            return Redirect::guest(route('user.login'));
    }

	public function getIndex()
	{
		return View::make('proximo.dashboard', array(
			'player' => $this->_getUser(),
		));
	}

	public function postMessage()
	{
		return "something happened";
	}

}
