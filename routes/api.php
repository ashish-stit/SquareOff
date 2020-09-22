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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', 'API\UserController@login')->name('login');
Route::post('signup', 'API\UserController@register')->name('signup');
Route::post('userprofile', 'API\UserController@userprofile')->name('userprofile');
Route::post('userdetails', 'API\UserController@userdetails')->name('userdetails');
Route::post('/changepassword','ResetPasswordController@changepassword');
Route::post('sendotp', 'API\UserController@sendotp')->name('sendotp');
Route::post('forgetotp', 'ResetPasswordController@forgetotp')->name('forgetotp');
Route::post('/resetotpverify','ResetPasswordController@resetotpverify')->name('resetotpverify');
Route::post('resetPass', 'ResetPasswordController@resetPass')->name('resetPass');
Route::post('verifyOtp', 'API\UserController@verifyOtp')->name('verifyOtp');
Route::post('verified', 'API\UserController@verified')->name('verified');
Route::get('/sport','TableTennisController@sport')->name('sport');
Route::get('/state','TableTennisController@state')->name('state');
Route::get('/country','TableTennisController@country')->name('country');
Route::post('/district','TableTennisController@district')->name('district');
Route::post('/teniscircle','TableTennisController@teniscircle')->name('teniscircle');
Route::post('/showcircle','TableTennisController@showcircle')->name('showcircle');
Route::post('/redirect/facebook','API\UserController@facebook')->name('facebook');
Route::post('/facebookprofl','API\UserController@facebookprofl')->name('facebookprofl');
Route::post('/circlerequest','CircleRequestController@circlerequest')->name('circlerequest');
Route::post('/circlerequestlist','CircleRequestController@circlerequestlist')->name('circlerequestlist');
Route::post('/shownotification','CircleRequestController@shownotification')->name('shownotification');
Route::post('/urlrequest','CircleRequestController@urlrequest')->name('urlrequest');
Route::post('/isaccept','CircleRequestController@isaccept')->name('isaccept');
Route::get('/challengecirclelist','ChallengeController@challengecirclelist')->name('challengecirclelist');
Route::post('/challengeuserlist','ChallengeController@challengeuserlist')->name('challengeuserlist');
Route::post('/challengerequest','ChallengeController@challengerequest')->name('challengerequest');
Route::post('/challengeaccept','ChallengeController@challengeaccept')->name('challengeaccept');
Route::post('/circlelist','TableTennisController@circlelist')->name('circlelist');
Route::post('/singlecircle','TableTennisController@singlecircle')->name('singlecircle');
Route::post('/userlist','NewGameController@userlist')->name('userlist');
Route::post('/gamerequest','NewGameController@gamerequest')->name('gamerequest');
Route::post('/confirm','NewGameController@confirm')->name('confirm');
Route::post('/reject','NewGameController@reject')->name('reject');
Route::post('/sendNotification','NewGameController@sendNotification')->name('sendNotification');
Route::post('/turnamentuserlist','TurnamentController@turnamentuserlist')->name('turnamentuserlist');
Route::get('/turnamentcirclelist','TurnamentController@turnamentcirclelist')->name('turnamentcirclelist');
Route::post('/sendInvothercircle','TurnamentController@sendInvothercircle')->name('sendInvothercircle');
Route::post('/sendInvmycircle','TurnamentController@sendInvmycircle')->name('sendInvmycircle');
Route::post('/mycirclelist','TurnamentController@mycirclelist')->name('mycirclelist');
Route::post('/myuserlist','TurnamentController@myuserlist')->name('myuserlist');
Route::post('/turnamentlist','TurnamentController@turnamentlist')->name('turnamentlist');
Route::post('/turnamentrequest','TurnamentController@turnamentrequest')->name('turnamentrequest');
Route::post('/turnamentaccept','TurnamentController@turnamentaccept')->name('turnamentaccept');
