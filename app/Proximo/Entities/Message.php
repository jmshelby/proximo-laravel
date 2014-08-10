<?php namespace Proximo\Entities;
/*
 * Need to Make sure the following gets ran, until we can do if from the app
 *   db.proximo.message.ensureIndex( { loc : "2dsphere" } )
 *
 */
class Message extends \Moloquent
{

	protected $table = 'proximo.message';

	// == Factories ==============================================================

	public static function createFromBroadcast($user, $content, $lat, $long)
	{
		$message = new static;

		$message->user()->associate($user);
		$message->content = $content;

		$message->lat = $lat;
		$message->long = $long;

		$message->save();
		return $message;
	}

	// == Relationships ==========================================================

	public function user()
	{
		return $this->belongsTo('Proximo\Entities\User');
	}

	// == Scopes =================================================================

	public function scopeNewestFirst($q)
	{
		return $q->orderBy('created_at', 'desc');
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

}
