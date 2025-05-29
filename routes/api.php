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

Route::namespace('Api')->prefix('v1')->group(function (){
    Route::match(['get', 'post'], 'send', 'ApiController@sendSms');
    Route::match(['get', 'post'],'balance', 'ApiController@showBalance');
    Route::match(['get', 'post'],'send-load', 'FlexiloadApiController@send_flexi_load');
});

Route::namespace('Api')->prefix('v2')->group(function (){
    Route::match(['get', 'post'], 'send', 'SmsSendDesktopController@sendSmsDesktop');
    Route::match(['get', 'post'],'balance', 'SmsSendDesktopController@showBalance');
});

Route::namespace('Api')->prefix('android')->group(function (){
    Route::match(['get', 'post'], 'login', 'AndroidApiController@androidApiLogin');
    Route::match(['get', 'post'],'flexiload-api', 'AndroidApiController@androidFlexiloadApi');
    Route::match(['get', 'post'],'flexiload-report', 'AndroidApiController@apdroidFlexiReport');
    Route::match(['get', 'post'],'flexiload-packages', 'AndroidApiController@flexiloadPackagesList');
    Route::match(['get', 'post'],'flexiload-packages-category', 'AndroidApiController@flexiloadPackagesCategoryList');

});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
