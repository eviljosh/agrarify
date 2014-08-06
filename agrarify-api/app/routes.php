<?php

use \Illuminate\Support\Facades\Hash;

Route::get('/', 'HomeController@showWelcome');

Route::get('/josh', function()
{
    // HASHING
//    $string = "Like a father's mourning dress.";
//    $hash = Hash::make($string, ['rounds' => 13]);
//    $checks_out = Hash::check($string, $hash) ? 'It checks out.' : 'It does not check out.';
//    dd($string . ' hashes to: ' . $hash . ' and... ' . $checks_out);

    // RANDOM STRING
    $string = str_random(40);
    dd($string);
});

Route::post('/v1/oauth_consumer', ['as' => 'v1.oauthconsumer.create', 'uses' => 'OauthController@createConsumer']);
Route::post('/v1/access_token', ['as' => 'v1.accesstoken.create', 'uses' => 'OauthController@createAccessToken']);