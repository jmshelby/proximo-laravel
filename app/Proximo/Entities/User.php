<?php namespace Proximo\Entities;

//class User extends \Proximo\GenePool\Models\Mongo\Root {
class User extends \Moloquent
{

	protected $table = 'proximo.user';

	protected $guarded = array();

	// == Factories ==============================================================

	public static function getFromAuthUser($userId)
	{
		if (is_object($userId))
			$userId = $userId->id;
		return static::where('auth_user_id',$userId)->first();
	}

	public static function createFromAuthUser($authUser)
	{
		if ($user = static::getFromAuthUser($authUser))
			return $user;
		$user = new static;
		$user->authUser()->associate($authUser);
		$user->username = $authUser->username;
		$user->save();
		return $user;
	}

	// == Relationships ==========================================================

	public function authUser()
	{
		return $this->belongsTo('User', 'auth_user_id');
	}

	public function messages()
	{
		return $this->hasMany('Proximo\Entities\Message');
	}

}
