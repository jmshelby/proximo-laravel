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

}
