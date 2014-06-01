<?php namespace Proximo\Controllers\Frontend;

use App;
use View;
use Input;
use Redirect;
use Exception;

class MapViewController extends \Proximo\GenePool\Controller\Frontend\Root {

    public function __construct()
    {
        //$this->service = App::make('proximo.service.frontend');
    }

	public function getIndex()
	{
		return View::make('map_view.index');
	}

}
