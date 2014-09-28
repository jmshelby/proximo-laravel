<?php namespace Proximo\Mongodb\Eloquent;

/**
 * 
 */
trait GeospatialTrait
{

	public static function bootGeospatialTrait()
	{
		static::addGlobalScope(new GeoNearCommandMacro);
	}

}
