<?php
use Agrarify\Api\Exception\ApiErrorException;
use Illuminate\Http\Response as HttpResponse;

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useDailyFiles(storage_path().'/logs/laravel.log');

// Agrarify request/response logging
App::before(function($request)
{
    //
});

App::after(function($request, $response)
{
    $log_string = $request->getMethod() . ' ' . $request->getPathInfo() . ' returned ' . $response->getStatusCode();

    $request_content = $request->getContent();
    $request_json = json_decode($request_content, true);
    if (array_key_exists('item', $request_json))
    {
        if (array_key_exists('password', $request_json['item'])) {
            $request_json['item']['password'] = '*****';
        }
        if (array_key_exists('existing_password', $request_json['item'])) {
            $request_json['item']['existing_password'] = '*****';
        }
    }
    $request_content = $request_json ?: $request_content;

    $response_content = $response->getContent();
    $response_json = json_decode($response_content, true);
    $response_content = $response_json ?: $response_content;

    $log_context = [
        'request_authorization' => $request->headers->get('authorization'),
        'request_content_type' => $request->getContentType(),
        'request_query_parameters' => $request->query,
        'request_body' => $request_content,
        'response_body' => $response_content,
    ];

    if ($response->getStatusCode() > 299)
    {
        Log::error($log_string, $log_context);
    }
    else
    {
        Log::warning($log_string, $log_context);
    }
});

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(ApiErrorException $exception, $code) {
    return Response::json($exception->getErrors(), $exception->getHttpStatusCode());
});

App::error(function(Symfony\Component\HttpKernel\Exception\HttpException $exception, $code) {
    $message = $exception->getMessage() ?: 'Please refer to status code.';
    return Response::json(
        ['errors' => ['message' => $message, 'code' => ApiErrorException::ERROR_CODE_NO_CODE_ASSIGNED]],
        $exception->getStatusCode()
    );
});

App::error(function(\Whoops\Exception\ErrorException $exception, $code) {
    return Response::json(
        ['errors' => ['message' => $exception->getMessage(), 'code' => ApiErrorException::ERROR_CODE_NO_CODE_ASSIGNED]],
        HttpResponse::HTTP_INTERNAL_SERVER_ERROR
    );
});

App::fatal(function($exception) {
    return Response::json(
        ['errors' => ['message' => $exception->getMessage(), 'code' => ApiErrorException::ERROR_CODE_NO_CODE_ASSIGNED]],
        HttpResponse::HTTP_INTERNAL_SERVER_ERROR
    );
});

//App::error(function(Exception $exception, $code)
//{
//	Log::error($exception);
//});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';
