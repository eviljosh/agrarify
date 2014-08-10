<?php

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;

class HomeController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('hello');
	}

    public function showBasicStatus()
    {
        $account = \Agrarify\Models\Accounts\Account::take(1)->get();
        $environment = App::environment();
        if ($account and $environment)
        {
            return Response::json(['status' => 'ok', 'environment' => $environment], HttpResponse::HTTP_OK);
        }
        else
        {
            return Response::json([
                    'status' => 'could not find account in database',
                    'environment' => $environment
                ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
