<?php namespace Proximo\Controllers\Frontend\Ajax;

use App;
use View;
use Input;
use Redirect;
use Response;
use Exception;
use Illuminate\Support\Collection as Collection;

class MessageController extends \Proximo\GenePool\Controller\Frontend\Root {

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
            return $this->_response_requireAuth();
    }

	// ================================================================

	protected function _response_requireAuth()
	{
		return Response::json(array(
			'status' => 'auth_required',
			'statusText' => 'Authorization is required for this request. Redirect to: /auth/login',
		));
	}

	protected function _response_requirePosition()
	{
		return Response::json(array(
			'status' => 'position_required',
			'statusText' => 'Position is required for this request. Make call to POST /ajax/message/position',
		));
	}

	protected function _response_success()
	{
		return Response::json(array(
			'status' => 'success',
		));
	}

	// ================================================================

	protected function _updateLastPosition($latField = 'latitude', $longField = 'longitude')
	{
		$lat = Input::get('latitude', null);
		$long = Input::get('longitude', null);
		$this->service->lastPosition($lat, $long);
	}

	// ================================================================

	public function getPosition()
	{
		$pos = $this->service->lastPosition();
		if ($pos) {
			return Response::json(array(
				'status'   =>'success',
				'response' => array(
					'latitude'  => $pos->lat,
					'longitude' => $pos->long,
				),
			));
		} else {
			return $this->_response_requirePosition();
		}
	}

	public function postPosition()
	{
		if (Input::has('latitude') && Input::has('longitude')) {
			$this->_updateLastPosition();
			return $this->_response_success();
		}
		return Response::json(array(
			'status' => 'fail',
			'statusText' => 'Missing/Incorrect Parameters, "latitude" and "longitude" required',
		));
	}

	public function anyFetch()
	{
		// Update lat/long (if passed)
		$this->_updateLastPosition();

		// TODO -- make sure lat/long exists (in service 'lastPosition')

		try {
			$messages = $this->service->getUserMessages();
		} catch (Exception $e) {
			return Response::json(array(
				'status' => 'fail',
				'messages' => array(
					'text' => $e->getMessage(),
				),
			));
		}

		return Response::json(array(
			'status'   => 'success',
			'response' => $this->_formatMessage($messages),
		));
	}

	public function anyBroadcast()
	{
		// Update lat/long (if passed)
		$this->_updateLastPosition();

		// TODO -- make sure lat/long exists (in service 'lastPosition')

		$message = Input::get('message');

		try {
			$pos = $this->service->lastPosition();
			$newMessage = $this->service->userPostsMessage($message, $pos->lat, $pos->long);
		} catch (Exception $e) {
			return Response::json(array(
				'status' => 'fail',
				'messages' => array(
					'text' => $e->getMessage(),
				),
			));
		}
		return Response::json(array(
			'status' => 'success',
			'statusText' => 'Message Successfully posted',
			'response' => $this->_formatMessage($newMessage),
		));
	}

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
