<?php namespace Proximo;

use Proximo\Entities\Message;
use Proximo\Entities\User;

class ProximoManager
{

	// Convenience function for converting scalar values representing a
	//  user, to an actual user object
	public function user($sourceIdentifier, $graceful=false)
	{
		$user = null;

		if ( $sourceIdentifier instanceof User )
			return $sourceIdentifier;

		if (!is_object($sourceIdentifier))
			$user = $this->getUserById($sourceIdentifier);

		if (!$user && !$graceful) {
			// TODO -- Make custom exception for this
			throw new Exception("Cannot fetch unknown user ( TODO - Make custom exception for this)");
		}

		return $user;
	}

	public function getUserById($id)
	{
		return User::find($id);
	}
	public function getMessagesNear($lat, $long)
	{
		$lat = (float) $lat;
		$long = (float) $long;
		$point = array(
			'type' => "Point",
			'coordinates' => array($long, $lat),
		);
		
		$q = Message::whereRaw(
			array(
				'loc' => array(
					'$nearSphere' => array('$geometry' => $point),
					'$maxDistance' => 20000,
				),
			)
		);

		$q->newestFirst();
 
		$messages = $q->get();
		return $messages;
	}

	public function userPostsMessage($user, $message, $lat, $long)
	{
		$user = $this->user($user);
		// TODO - Validate Message
		// TODO - Location
		$newMessage = Message::createFromBroadcast($user, $message, $lat, $long);
		return true;
	}




/*
	public function userPostsMessage($user, $message, $lat, $long)
	{
		$user = $this->user($user);
		// TODO - Validate Message
		// TODO - Location
		$newMessage = Message::createFromBroadcast($user, $message, $lat, $long);
		return true;
	}
*/

/*
	public function getUserMessages($user)
	{
		$user = $this->user($user);
		$messages = $user->messages();
		$messages->newestFirst();
		$messages->take(20);
		return $messages->get();
	}
*/







/*
	public function __construct(Dispatch $dispatch)
	{
		$this->_dispatch = $dispatch;
	}
*/


}
