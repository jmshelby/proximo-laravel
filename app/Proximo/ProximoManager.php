<?php namespace Proximo;

use Proximo\Entities\Message;
use Proximo\Entities\User;

use Illuminate\Support\Collection;

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

	public function getNearestMessages($lat, $long, $limit = null)
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
				),
			)
		);

		if (is_null($limit))
			$limit = 10;
		$q->limit($limit);
 
		// Get the in-memory collection
		$messages = $q->get();

		// Sorting the in-mem collection
		$messages->sortByDesc(function($item) { return @$item->created_at->timestamp; });

		return $messages;
	}

	// This is the old function that returns messages with x radius of location
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
		return $newMessage;
	}



	public function getMessagesNearWithDist($lat, $long, $mult = 1)
	{
		echo "<pre>","\n";
		echo "","\n";
		echo "Going to show messages near: lat: $lat, long: $long","\n";
		echo "","\n";
		echo "","\n";
		echo "","\n";
		echo "","\n";

		$lat = (float) $lat;
		$long = (float) $long;

		$point = array(
			'type' => "Point",
			'coordinates' => array($long, $lat),
		);
		
		$db = \DB::getMongoDB();

		$r = $db->command(array(
			'geoNear' => 'proximo.message',
			'near' => $point,
			'spherical' => true,
			//'distanceMultiplier' => (3959 * pi()) / 180,
			//'distanceMultiplier' => 3959,
			//'distanceMultiplier' => $mult,
			//'distanceMultiplier' => 2.457495 / 3959, // This is as close as I could get, still not sure the right way
		));


		if (!isset($r['results'])) {
			return new Collection;
		}

		$rCollection = new Collection($r['results']);
		$distances = $rCollection->lists('dis');
		$objects = $rCollection->lists('obj');

		$collection = (new Message)->hydrate($objects);
		foreach($collection as $index => $message) {
			$message->command_metadata = (object) array(
				'distance' => $distances[$index],
			);
		}

		echo "Messages:","\n";
		print_r($objects);
		print_r($distances);
		print_r($collection->toArray());
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
