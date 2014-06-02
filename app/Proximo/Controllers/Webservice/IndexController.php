<?php namespace Proximo\Controllers\Webservice;

use App;
use View;
use Input;
use Redirect;
use Response;
use Exception;
use Illuminate\Support\Collection as Collection;

class IndexController extends \Proximo\GenePool\Controller\Webservice\Root
{

    public $service;

    public function __construct()
    {
        $this->service = App::make('proximo.service.api');
    }

	public function _getUser()
	{
		return $this->service->getUser();
	}

	// ========================================================================

	protected function _response_exception(Exception $e)
	{
        return Response::json(array(
            'status' => 'fail',
			'statusText' => $e->getMessage(),
        ));
	}

	protected function _response_success($response = null, $statusText = null)
	{
		$returnArray = array(
			'status' => 'success',
			'response' => $response,
		);
		if (!is_null($statusText)) {
			$returnArray['statusText'] = $statusText;
		}
		return Response::json($returnArray);
	}

	// ========================================================================

	public function getUser()
	{
		try {
			$user = $this->_getUser();
		} catch (Exception $e) {
			return $this->_response_exception($e);
		} 
		return $this->_response_success($this->_formatUser($user));
	}

	public function getUserPost()
	{
		try {
			$user = $this->_getUser();
			$user->fill(Input::except('username'));
			$user->save();
		} catch (Exception $e) {
			return $this->_response_exception($e);
		}
		return $this->_response_success($this->_formatUser($user));
	}

    public function anyMessages()
    {
        try {
			// Added this because the api will require the user to fetch messages
			//  -- eventhough we're not doing anything with it now.
			$user = $this->_getUser();

            $messages = $this->service->getUserMessages();
        } catch (Exception $e) {
			return $this->_response_exception($e);
        }
		return $this->_response_success($this->_formatMessage($messages));
    }

    public function anyPostMessage()
    {
        try {
        	$message = Input::get('content');
            $lat = $this->service->getParamLatitude();
            $long = $this->service->getParamLongitude();
            $newMessage = $this->service->userPostsMessage($message, $lat, $long);
        } catch (Exception $e) {
			return $this->_response_exception($e);
        }
		return $this->_response_success(
			$this->_formatMessage($newMessage),
            'Message Successfully posted'
		);
    }

	// ===== Formatters =======================================================

    public function _formatMessage($object)
    {
        if ($object instanceof Collection) $object = $object->all();
        if (is_array($object)) return array_map(__METHOD__, $object);
        $formatted = (object) array(
            'id' => $object->id,
            'content' => $object->content,
            'location' => array(
                'latitude' => @$object->loc[coordinates][0],
                'longitude' => @$object->loc[coordinates][1],
            ),
            'date' => (string) $object->created_at,
            'user' => $this->_formatUser($object->user),
        );
        return $formatted;
    }

    public function _formatUser($object)
    {
        if ($object instanceof Collection) $object = $object->all();
        if (is_array($object)) return array_map(__METHOD__, $object);
        $formatted = (object) array(
            'id' => $object->id,
            'username' => $object->username,
            'created_at' => (string) $object->created_at,
            'updated_at' => (string) $object->updated_at,
        );
        return $formatted;
    }

}