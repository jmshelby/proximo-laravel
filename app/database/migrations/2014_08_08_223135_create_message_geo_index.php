<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Proximo\Entities\Message;

class CreateMessageGeoIndex extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Message::raw()->ensureIndex(array('loc' => '2dsphere'));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Message::raw()->deleteIndex('loc');
	}

}
