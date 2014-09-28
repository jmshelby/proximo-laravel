<?php namespace Proximo\Services;

use User as AuthUser;
use Proximo\ProximoManager;
use Proximo\Entities\User;
use Illuminate\Auth\AuthManager;

use Input;
use Exception;

class Api
{

	protected $_proximoMan;

	public function __construct(ProximoManager $proximoMan)
	{
		$this->_proximoMan = $proximoMan;
	}

// == temp - prototype easifiers =================

	public function getParamUserName()
	{
		if (!Input::has('username')) {
			throw new Exception("Username param required");
		} else if (!Input::get('username')) {
			throw new Exception("Username param required");
		}
		return Input::get('username');
	}

	protected function _createAuthUser($username)
	{
		$hash = \Hash::make('password');
		$user = new AuthUser;
		$user->username = $username;
		$user->password = $hash;
		$user->save();
		return $user;
	}

	protected $_authUser;
	public function getAuthUser()
	{
		if (is_null($this->_authUser)) {
			$user = AuthUser::whereUsername($this->getParamUserName())->first();
			if (!$user) {
				$user = $this->_createAuthUser($this->getParamUserName());
			}
			$this->_authUser = $user;
		}
		return $this->_authUser;
	}

// ===============================================

	public function getParamLatitude()
	{
		if (!Input::has('latitude')) {
			throw new Exception("Latitude param required");
		} else if (!Input::get('latitude')) {
			throw new Exception("Latitude param required");
		}
		return Input::get('latitude');
	}

	public function getParamLongitude()
	{
		if (!Input::has('longitude')) {
			throw new Exception("Longitude param required");
		} else if (!Input::get('longitude')) {
			throw new Exception("Longitude param required");
		}
		return Input::get('longitude');
	}

	protected $_user;
	public function getUser()
	{
		if (is_null($this->_user)) {
			$this->_user = User::createFromAuthUser($this->getAuthUser());
		}
		return $this->_user;
	}

	public function getGuestMessages()
	{
		$lat = $this->getParamLatitude();
		$long = $this->getParamLongitude();
		return $this->_proximoMan->getGuestMessages($lat, $long);
	}

	public function getUserMessages()
	{
		$user = $this->getUser();
		$lat = $this->getParamLatitude();
		$long = $this->getParamLongitude();
		return $this->_proximoMan->getUserMessages($user, $lat, $long);
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


/*
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


*/

}
