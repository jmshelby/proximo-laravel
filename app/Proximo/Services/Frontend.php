<?php namespace Proximo\Services;

use Proximo\ProximoManager;
use Proximo\Entities\User;
use Illuminate\Auth\AuthManager;

use Session;

class Frontend
{

	protected $_proximoMan;
	protected $_auth;

	public function __construct(ProximoManager $proximoMan, AuthManager $auth)
	{
		$this->_proximoMan = $proximoMan;
		$this->_auth = $auth;
	}

	protected function _loggedInOrFail()
	{
		if ($this->loggedInCheck()) return $this;
		// TODO -- Change this to custom exception
		throw new Exception("Session must be logged in");
	}

	public function loggedInCheck()
	{
		return $this->_auth->check();
	}

	public function getAuthUser()
	{
		$this->_loggedInOrFail();
		return $this->_auth->user();
	}

	protected $_user;
	public function getUser()
	{
		if (is_null($this->_user)) {
			$this->_user = User::createFromAuthUser($this->getAuthUser());
		}
		return $this->_user;
	}

	public function lastPosition($newLat = null, $newLong = null)
	{
		if (!empty($newLat) && !empty($newLong)) {
			Session::put('proximo.last_coords.lat', $newLat);
			Session::put('proximo.last_coords.long', $newLong);
			return $this;
		}
		$lat = Session::get('proximo.last_coords.lat', null);
		$long = Session::get('proximo.last_coords.long', null);
		if (is_null($lat) || is_null($long)) {
			return false;
		}
		return (object) array(
			'lat' => $lat,
			'long' => $long,
		);
	}

	public function getUserMessages()
	{
		$lastPosition = $this->lastPosition();
		if (!$lastPosition) {
			return array();
		}
		return $this->_proximoMan->getMessagesNear($lastPosition->lat, $lastPosition->long);
	}

	public function __call($method, $parameters)
	{
		// Forward to proximo manager, with player as first param
		$callback = array($this->_proximoMan, $method);
		array_unshift($parameters, $this->getUser());
		return call_user_func_array(
			$callback,
			$parameters
		);
	}

}
