<?php

use Agrarify\Api\Exception\ApiErrorException;
use Agrarify\Models\Oauth2\OauthAccessToken;
use Illuminate\Http\Response as HttpResponse;

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Agrarify Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the request contains a valid
| oauth access token, and store the account and token used.
|
*/

Route::filter('agrarify.api.auth', function()
{
    $request_is_authorized = false;
    $authorization = Request::header('Authorization');

    if ($authorization)
    {
        $auth_parts = explode(' ', $authorization);

        if (sizeof($auth_parts) == 2)
        {
            $auth_type = $auth_parts[0];
            $auth_token = $auth_parts[1];

            if ($auth_type == 'Bearer')
            {
                $token = OauthAccessToken::fetchByToken($auth_token);

                if ($token)
                {
                    $account = $token->account;
                    Session::put('account', $account);
                    Session::put('access_token', $token);
                    $request_is_authorized = true;
                }
            }
        }

    }

    if (!$request_is_authorized)
    {
        throw new ApiErrorException(
            ['message' => 'Bearer token not present or not authorized.'],
            HttpResponse::HTTP_UNAUTHORIZED
        );
    }

});

Route::filter('agrarify.api.auth_optional', function()
{
    $authorization = Request::header('Authorization');

    if ($authorization)
    {
        $auth_parts = explode(' ', $authorization);

        if (sizeof($auth_parts) == 2)
        {
            $auth_type = $auth_parts[0];
            $auth_token = $auth_parts[1];

            if ($auth_type == 'Bearer')
            {
                $token = OauthAccessToken::fetchByToken($auth_token);

                if ($token)
                {
                    $account = $token->account;
                    Session::put('account', $account);
                    Session::put('access_token', $token);
                }
            }
        }
    }
});

/*
|--------------------------------------------------------------------------
| Laravel Default Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
