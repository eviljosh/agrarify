<?php
use Agrarify\Api\Exception\ApiErrorException;
use Agrarify\Models\Oauth2\OauthConsumer;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class OauthController extends ApiController {

	/**
	 * Creates an oauth2 consumer
	 *
	 * @return Response
	 */
	public function createConsumer()
	{
        $payload = $this->getRequestPayloadItem();

        // First, ensure that request has permission to create new oauth consumers
        if (Hash::check($payload['authority'], Config::get('agrarify.consumer_creation_authority')))
        {
            $consumer = new OauthConsumer($payload);
            $consumer->save();
            return Response::json(['consumer' => $consumer, 'input_seen' => $payload]);
        }

        // If insufficient permission, return an error
        return $this->sendErrorForbiddenResponse(['message' => 'Insufficient authority to perform this action.']);
	}

    /**
     * Creates an oauth2 access token
     *
     * @return Response
     */
    public function createAccessToken()
    {
        // TODO: make route, check consumer id, throw 401, generate empty account if needed, check account credentials otherwise, generate random strings and such, save, return
    }

}
