<?php namespace Proximo\Entities;

/*
 * Need to Make sure the following gets ran, until we can do if from the app
 *   db.proximo.message.ensureIndex( { loc : "2dsphere" } )
 *
 *
 *
 */


class Message extends \Moloquent {

	protected $table = 'proximo.message';

	// == Factories ==============================================================

	public static function createFromBroadcast($user, $content, $lat, $long)
	{
        $message = new static;

        $message->user()->associate($user);
        $message->content = $content;

// TODO - create and attach geo location object
// Example: { loc : { type : "Point" , coordinates : [ 40, 5 ] } } // has to be in order: long, lat
$lat = (float) $lat;
$long = (float) $long;
$message->loc = array(
	'type' => 'Point',
	'coordinates' => array($long, $lat),
);

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

}
