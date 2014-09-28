<?php namespace Proximo\Entities;

use Jenssegers\Mongodb\Model as Eloquent;
use Proximo\Mongodb\Eloquent\GeospatialTrait;

/*
 * Need to Make sure the following gets ran, until we can do if from the app
 *   db.proximo.delivered_message.ensureIndex( { location : "2dsphere" } )
 *   db.proximo.delivered_message.ensureIndex( { delivered_location : "2dsphere" } )
 *
 */
class DeliveredMessage extends Eloquent
{

	protected $table = 'proximo.delivered_message';

	use GeospatialTrait;


	public static function createFromMessageForUser(Message $message, User $user)
	{
		$delivery = new static;

		// The user delivered to
		$delivery->user()->associate($user);

		// The user delivered to
		$delivery->messageUser()->associate($message->user);
		$delivery->message()->associate($message);

		// The messages coords (for doing geo spatial queries on this model too)
		$delivery->latitude  = $message->latitude;
		$delivery->longitude = $message->longitude;

// TODO ? What other fields should be redundantly stored on the delivery record

		// The initial distance this message was from the user when delivered
		if (isset($message->command_metadata->distance)) {
			$delivery->distance = $message->command_metadata->distance;
		}

		$delivery->save();
		return $delivery;
	}

	// == Relationships ==========================================================

	public function user()
	{
		return $this->belongsTo('Proximo\Entities\User');
	}

	public function messageUser()
	{
		return $this->belongsTo('Proximo\Entities\User');
	}

	public function message()
	{
		return $this->belongsTo('Proximo\Entities\Message');
	}

	// == Scopes =================================================================

	public function scopeForUser($q, User $user)
	{
		return $q->where('user_id', $user->getKey());
	}


	// == Accessors ==============================================================

	public function getLocationAttribute($value)
	{
		return $this->getLocation();
	}

	public function getLocAttribute($value)
	{
		return $this->getLocation();
	}

	public function setLocationAttribute($value)
	{
		$this->setLocAttribute($value);
	}

	public function setLocAttribute($value)
	{
		if (is_array($value)) {
			$value = (object) $value;
		}

		$lat = null;
		if (isset($value->lat)) {
			$lat = $value->lat;
		} elseif (isset($value->latitude)) {
			$lat = $value->latitude;
		}

		$lng = null;
		if (isset($value->long)) {
			$lng = $value->long;
		} elseif (isset($value->longitude)) {
			$lng = $value->longitude;
		}

		$this->setLocation($lat, $lng);
	}

	public function setLatitudeAttribute($value)
	{
		$this->setLatitude($value);
	}

	public function setLatAttribute($value)
	{
		$this->setLatitude($value);
	}

	public function setLongitudeAttribute($value)
	{
		$this->setLongitude($value);
	}

	public function setLongAttribute($value)
	{
		$this->setLongitude($value);
	}




	public function getLatitudeAttribute($value)
	{
		return $this->getLatitude($value);
	}

	public function getLatAttribute($value)
	{
		return $this->getLatitude($value);
	}

	public function getLongitudeAttribute($value)
	{
		return $this->getLongitude($value);
	}

	public function getLongAttribute($value)
	{
		return $this->getLongitude($value);
	}





	// ===========================================================================

	public function getLongitude()
	{
		$curLoc = @$this->attributes['loc'];
		if (isset($curLoc['coordinates'][0]))
			return $curLoc['coordinates'][0];
		else
			return null;
	}

	public function getLatitude()
	{
		$curLoc = @$this->attributes['loc'];
		if (isset($curLoc['coordinates'][1]))
			return $curLoc['coordinates'][1];
		else
			return null;
	}

	public function getLong() { return $this->getLongitude(); }
	public function getLat() { return $this->getLatitude(); }

	public function getLocation()
	{
		return (object) array(
			'latitude' => $this->getLatitude(),
			'longitude' => $this->getLongitude(),

			'lat' => $this->getLatitude(),
			'long' => $this->getLongitude(),
		);
	}

	public function setLatitude($value)
	{
		$this->setLocation($value, null);
	}

	public function setLongitude($value)
	{
		$this->setLocation(null, $value);
	}

	public function setLocation($newLat = null, $newLng = null)
	{
		$lat = !is_null($newLat) ? $newLat : $this->getLatitude();
		$lng = !is_null($newLng) ? $newLng : $this->getLongitude();

		// Make sure they are floats
		$lat = (float) $lat;
		$lng = (float) $lng;

		// MongoDB Geo Location Object Format
		// Example: { loc : { type : "Point" , coordinates : [ 40, 5 ] } }
		// Has to be in order: long, lat
		$this->attributes['loc'] = array(
			'type'			=> 'Point',
			'coordinates'	=> array($lng, $lat),
		);
	}



	// == Factories ==============================================================

	// public static function createFromBroadcast($user, $content, $lat, $long)
	// {
	// 	$message = new static;
    //
	// 	$message->user()->associate($user);
	// 	$message->content = $content;
    //
	// 	$message->lat = $lat;
	// 	$message->long = $long;
    //
	// 	$message->save();
	// 	return $message;
	// }
    //
	// // == Relationships ==========================================================
    //
	// public function user()
	// {
	// 	return $this->belongsTo('Proximo\Entities\User');
	// }
    //
	// // == Scopes =================================================================
    //
	// public function scopeNewestFirst($q)
	// {
	// 	return $q->orderBy('created_at', 'desc');
	// }
    //
	// // == Accessors ==============================================================
    //
	// public function getLocationAttribute($value)
	// {
	// 	return $this->getLocation();
	// }
    //
	// public function getLocAttribute($value)
	// {
	// 	return $this->getLocation();
	// }
    //
	// public function setLocationAttribute($value)
	// {
	// 	$this->setLocAttribute($value);
	// }
    //
	// public function setLocAttribute($value)
	// {
	// 	if (is_array($value)) {
	// 		$value = (object) $value;
	// 	}
    //
	// 	$lat = null;
	// 	if (isset($value->lat)) {
	// 		$lat = $value->lat;
	// 	} elseif (isset($value->latitude)) {
	// 		$lat = $value->latitude;
	// 	}
    //
	// 	$lng = null;
	// 	if (isset($value->long)) {
	// 		$lng = $value->long;
	// 	} elseif (isset($value->longitude)) {
	// 		$lng = $value->longitude;
	// 	}
    //
	// 	$this->setLocation($lat, $lng);
	// }
    //
	// public function setLatitudeAttribute($value)
	// {
	// 	$this->setLatitude($value);
	// }
    //
	// public function setLatAttribute($value)
	// {
	// 	$this->setLatitude($value);
	// }
    //
	// public function setLongitudeAttribute($value)
	// {
	// 	$this->setLongitude($value);
	// }
    //
	// public function setLongAttribute($value)
	// {
	// 	$this->setLongitude($value);
	// }
    //
	// // ===========================================================================
    //
	// public function getLongitude()
	// {
	// 	$curLoc = @$this->attributes['loc'];
	// 	if (isset($curLoc['coordinates'][0]))
	// 		return $curLoc['coordinates'][0];
	// 	else
	// 		return null;
	// }
    //
	// public function getLatitude()
	// {
	// 	$curLoc = @$this->attributes['loc'];
	// 	if (isset($curLoc['coordinates'][1]))
	// 		return $curLoc['coordinates'][1];
	// 	else
	// 		return null;
	// }
    //
	// public function getLong() { return $this->getLongitude(); }
	// public function getLat() { return $this->getLatitude(); }
    //
	// public function getLocation()
	// {
	// 	return (object) array(
	// 		'latitude' => $this->getLatitude(),
	// 		'longitude' => $this->getLongitude(),
    //
	// 		'lat' => $this->getLatitude(),
	// 		'long' => $this->getLongitude(),
	// 	);
	// }
    //
	// public function setLatitude($value)
	// {
	// 	$this->setLocation($value, null);
	// }
    //
	// public function setLongitude($value)
	// {
	// 	$this->setLocation(null, $value);
	// }
    //
	// public function setLocation($newLat = null, $newLng = null)
	// {
	// 	$lat = !is_null($newLat) ? $newLat : $this->getLatitude();
	// 	$lng = !is_null($newLng) ? $newLng : $this->getLongitude();
    //
	// 	// Make sure they are floats
	// 	$lat = (float) $lat;
	// 	$lng = (float) $lng;
    //
	// 	// MongoDB Geo Location Object Format
	// 	// Example: { loc : { type : "Point" , coordinates : [ 40, 5 ] } }
	// 	// Has to be in order: long, lat
	// 	$this->attributes['loc'] = array(
	// 		'type'			=> 'Point',
	// 		'coordinates'	=> array($lng, $lat),
	// 	);
	// }

}
