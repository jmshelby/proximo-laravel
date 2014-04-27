<?php namespace Proximo\Services;

use Proximo\ProximoManager;
use Proximo\Entities\User;
use Illuminate\Auth\AuthManager;

class Frontend {

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

}
