<?php

use Illuminate\Support\Facades\App;

class ApiController extends BaseController {

	protected function getRequestPayloadItem()
    {
        $payload = Request::all();
        if (is_array($payload) and array_key_exists('item', $payload))
        {
            return $payload['item'];
        }

        App::abort(400, 'Unable to find or parse request payload. Is your Content-Type set correctly?');
    }

}
