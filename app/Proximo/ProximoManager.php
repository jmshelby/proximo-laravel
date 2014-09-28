<?php namespace Proximo;

use Carbon\Carbon;

use Proximo\Entities\User;
use Proximo\Entities\Message;
use Proximo\Entities\DeliveredMessage;

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

	// ==========================================================================

	public function userPostsMessage($user, $message, $lat, $long)
	{
		$user = $this->user($user);
		// TODO - Validate Message
		// TODO - Location
		$newMessage = Message::createFromBroadcast($user, $message, $lat, $long);
		return $newMessage;
	}

	// ==========================================================================

	public function getGuestMessages($lat, $long, $limit = null)
	{
		return $this->getMessagesNear($lat, $long, $limit);
	}

	public function getMessagesNear($lat, $long, $limit = null)
	{
		// TODO -- Move this somewhere else
		$minutes = 30;

		// TODO -- Move this somewhere else
		if (is_null($limit)) $limit = 30;

		// Limited to the last x minutes
		$q = Message::where('created_at', '>', Carbon::now()->subMinutes($minutes));
		$q->limit($limit);

		// Get the in-memory collection
		$messages = $q->geoNear($lat, $long);

		// If count is less than the limit, return something else
		if ($messages->count() < $limit) {
\Log::info(__METHOD__.": Not enough messages found, returning messages unbounded by location..");
			// Use the $limit most recent messages (with distances)
			$messages = Message::limit($limit)->geoNear($lat, $long);
		}

		// Sorting the in-mem collection (since the nearsphere is sorted by 
		// distance first)
		$messages->sortByDesc(function($item) { return @$item->created_at->timestamp; });

		return $messages;
	}

	// ==========================================================================

	public function getUserMessages($user, $lat, $long)
	{
		// First - Deliver new messages to user from this location
		$this->_deliverMessagesToUser($user, $lat, $long);

		// Second - Return delivered messages for this location
		$messages = $this->_getDeliveredMessages($user, $lat, $long);

		return $messages;
	}

	protected function _deliverMessagesToUser($user, $lat, $long)
	{
		$messagesNear = $this->getMessagesNear($lat, $long);

		$existingDeliveries = DeliveredMessage::forUser($user);
		$existingDeliveries->whereIn('message_id', $messagesNear->lists('id'));
		$existingDeliveries = $existingDeliveries->get();

		foreach($messagesNear->getDictionary() as $id => $message) {

			$delivery = $existingDeliveries->first(function($key, $possible) use ($id) {
				return ($possible->message_id == $id);
			});

			if ($delivery) {
				// If delivery already exists, update with max of it's distance
				$delivery->distance = $message->command_metadata->distance;
				$delivery->save();
			} else {
				// Create new delivery for this message
				$delivery = DeliveredMessage::createFromMessageForUser($message, $user);
			}
		}

	}

	protected function _getDeliveredMessages($user, $lat, $long)
	{
		$q = DeliveredMessage::forUser($user);
		$q->with('message');
		$q->limit(100); // Just for processing sake
		$deliveries = $q->geoNear($lat, $long);
\Log::info(__METHOD__.": Delivery query results: \n".print_r($deliveries->toArray(),true));

		$messages = new Collection;
		foreach($deliveries as $delivery) {

			$currentDistance = $delivery->command_metadata->distance;

			// Filter out messages where the current distance is greater than the delivered distance
			if ($currentDistance > $delivery->distance) continue;

			$messages->push($delivery->message);
		}

		// Sorting the in-mem collection (since the nearsphere is sorted by 
		// distance first)
		$messages->sortByDesc(function($item) { return @$item->created_at->timestamp; });

		return $messages;
	}


}
