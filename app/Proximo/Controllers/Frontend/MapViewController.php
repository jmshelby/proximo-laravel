<?php namespace Proximo\Controllers\Frontend;

use App;
use View;
use Input;
use Redirect;
use Exception;

class MapViewController extends \Proximo\GenePool\Controller\Frontend\Root
{

    public $service;

    public function __construct()
    {
        $this->service = App::make('proximo.service.frontend');
        $this->beforeFilter('@filterRequest');
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
		return View::make('map_view.index');
	}

}
