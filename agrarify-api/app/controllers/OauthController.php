<?php
use Agrarify\Api\Exception\ApiErrorException;
use Agrarify\Models\Oauth2\OauthConsumer;
use Agrarify\Transformers\AgrarifyTransformer;
use Agrarify\Transformers\OauthConsumerTransformer;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class OauthController extends ApiController {

    /**
     * @var AgrarifyTransformer
     */
    private $consumer_transformer;

    /**
     * @var AgrarifyTransformer
     */
    private $token_transformer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->consumer_transformer =  new OauthConsumerTransformer();
        // TODO - token transformer
    }

    /**
	 * Creates an oauth2 consumer
	 *
	 * @return Response
	 */
	public function createConsumer()
	{
        $this->transformer = $this->consumer_transformer;
        $payload = $this->getRequestPayloadItem();

        // First, ensure that request has permission to create new oauth consumers
        if (Hash::check($payload['authority'], Config::get('agrarify.consumer_creation_authority')))
        {
            $consumer = new OauthConsumer($payload);
            $this->assertValid($consumer);
            $consumer->save();
            return $this->sendSuccessResponse($consumer);
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
