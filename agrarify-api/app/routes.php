<?php

use \Illuminate\Support\Facades\Hash;

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
