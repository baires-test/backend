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

Route::get('images', 'ImageController@index')->middleware('cors');
Route::get('images/deleted', 'ImageController@deleted')->middleware('cors');
Route::get('images/{id}', 'ImageController@show')->middleware('cors');
Route::post('images', 'ImageController@store')->middleware('cors');
Route::put('images/{id}', 'ImageController@update')->middleware('cors');
Route::delete('image/{id}', 'ImageController@delete')->middleware('cors');