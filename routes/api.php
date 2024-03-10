<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', 'App\Http\Controllers\API\AuthController@login');
Route::post('/register', 'App\Http\Controllers\API\AuthController@register');

Route::post('user/image', 'App\Http\Controllers\API\UsersController@file_upload');
Route::post('contact/image', 'App\Http\Controllers\API\ContactsController@file_upload');
Route::post('item/image', 'App\Http\Controllers\API\LostsFoundsController@file_upload');

Route::get('all/user', 'App\Http\Controllers\API\UsersController@users');

Route::resource('bill', 'App\Http\Controllers\API\BillsController');
Route::resource('contact', 'App\Http\Controllers\API\ContactsController');
Route::resource('facility', 'App\Http\Controllers\API\FacilitiesController');
Route::resource('house/type', 'App\Http\Controllers\API\HouseTypesController');
Route::resource('lost/found', 'App\Http\Controllers\API\LostsFoundsController');
Route::resource('comment', 'App\Http\Controllers\API\LostFoundCommentsController');
Route::resource('reservation', 'App\Http\Controllers\API\ReservationsController');
Route::resource('user', 'App\Http\Controllers\API\UsersController');