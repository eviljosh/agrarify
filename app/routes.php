<?php

use \Illuminate\Support\Facades\Hash;

// Status endpoints
Route::get('/', 'HomeController@showBasicStatus');

// Oauth 2 endpoints
Route::post('/v1/oauth_consumer', ['as' => 'v1.oauthconsumers.create', 'uses' => 'OauthController@createConsumer']);
Route::post('/v1/access_token', ['as' => 'v1.accesstokens.create', 'uses' => 'OauthController@createAccessToken']);

// Confirmation endpoints
Route::get('email_confirmation/{token}', ['as' => 'confirmation.email', 'uses' => 'ConfirmationTokensController@getConfirmed']);

// Forgotten password endpoint
Route::post('v1/forgotten_password', ['as' => 'v1.accounts.forgottenpassword', 'uses' => 'AccountsController@forgottenPassword']);

// Veggie endpoints available without login
Route::get('/v1/veggies/options', ['as' => 'v1.veggies.optionslist', 'uses' => 'VeggiesController@listOptions']);
Route::get('/v1/search/test/veggies', ['as' => 'v1.veggies.testsearch', 'uses' => 'VeggiesController@testSearch']);  // TODO: implement a real search controller once elastic search is up

// API v1 Resource endpoints
Route::group(['prefix' => 'v1', 'before' => 'agrarify.api.auth'], function () {

    Route::resource('accounts', 'AccountsController');

    Route::get('/accounts/me/profile', ['as' => 'v1.accountprofiles.showforaccount', 'uses' => 'AccountProfilesController@showForAccount']);
    Route::put('/accounts/me/profile', ['as' => 'v1.accountprofiles.updateforaccount', 'uses' => 'AccountProfilesController@updateForAccount']);
    Route::get('/profiles/{slug}', ['as' => 'v1.accountprofiles.show', 'uses' => 'AccountProfilesController@show']);

    Route::get('/accounts/me/locations', ['as' => 'v1.locations.list', 'uses' => 'LocationsController@listLocations']);
    Route::get('/accounts/me/locations/{id}', ['as' => 'v1.locations.show', 'uses' => 'LocationsController@show']);
    Route::post('/accounts/me/locations', ['as' => 'v1.locations.create', 'uses' => 'LocationsController@create']);
    Route::put('/accounts/me/locations/{id}', ['as' => 'v1.locations.update', 'uses' => 'LocationsController@update']);
    Route::delete('/accounts/me/locations/{id}', ['as' => 'v1.locations.delete', 'uses' => 'LocationsController@deleteLocation']);

    Route::get('veggies/messages', ['as' => 'v1.messages.veggiemessageslist', 'uses' => 'MessagesController@listVeggieMessages']);
    Route::get('veggies/{id}/messages', ['as' => 'v1.messages.veggiemessages', 'uses' => 'MessagesController@showVeggieMessages']);
    Route::post('veggies/{id}/messages', ['as' => 'v1.messages.createveggiemessage', 'uses' => 'MessagesController@createVeggieMessage']);
    Route::post('veggies/{veggie_id}/images', ['as' => 'v1.veggieimages.create', 'uses' => 'VeggieImagesController@create']);
    Route::put('veggies/{veggie_id}/images/{image_id}', ['as' => 'v1.veggieimages.update', 'uses' => 'VeggieImagesController@update']);
    Route::delete('veggies/{veggie_id}/images/{image_id}', ['as' => 'v1.veggieimages.destroy', 'uses' => 'VeggieImagesController@destroy']);
    Route::resource('veggies', 'VeggiesController');

    Route::put('messages/{id}', ['as' => 'v1.messages.update', 'uses' => 'MessagesController@update']);

});











// Experimentation endpoints
Route::get('/josh', function()
{
    // HASHING
    /*
    $string = 'stuff';
    $hash = Hash::make($string, ['rounds' => 13]);
    $checks_out = Hash::check($string, $hash) ? 'It checks out.' : 'It does not check out.';
    dd($string . ' hashes to: ' . $hash . ' and... ' . $checks_out);
    */

    // RANDOM STRING
    $string = str_random(40);
    dd($string);
});