<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::controller('auth',		'Proximo\Controllers\Frontend\AuthController');
Route::controller('ajax/message',	'Proximo\Controllers\Frontend\Ajax\MessageController');
Route::controller('webservice',		'Proximo\Controllers\Webservice\IndexController');
Route::controller('map-view',		'Proximo\Controllers\Frontend\MapViewController');
Route::controller('/',			'Proximo\Controllers\Frontend\IndexController');


