<?php


use Illuminate\Http\Request;
use App\Image;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'cors', 'prefix'=> '/images'], function() {
	Route::get('', 'ImageController@index')->middleware('cors');
	Route::get('/deleted', 'ImageController@deleted')->middleware('cors');

	Route::post('', 'ImageController@store')->middleware('cors');
	Route::put('/{id}', 'ImageController@update')->middleware('cors');
	Route::delete('/{id}', 'ImageController@delete')->middleware('cors');
});

Route::get('/download/{id}', 'ImageController@show');