<?php
use Agrarify\Models\Accounts\Account;
use Agrarify\Models\Accounts\AccountProfile;
use Agrarify\Models\Oauth2\OauthAccessToken;
use Agrarify\Models\Oauth2\OauthConsumer;
use Agrarify\Transformers\AgrarifyTransformer;
use Agrarify\Transformers\OauthAccessTokenTransformer;
use Agrarify\Transformers\OauthConsumerTransformer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class OauthController extends ApiController {

    /**
     * List of supported oauth grant types
     *
     * @var array
     */
    private static $supported_oauth_grant_types = [
        'password',
        'new',
    ];

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
        $this->token_transformer = new OauthAccessTokenTransformer();
    }

    /**
	 * Creates an oauth2 consumer
	 *
	 * @return Response
	 */
	public function createConsumer()
	{
        $this->transformer = $this->consumer_transformer;
        $payload = $this->assertRequestPayloadItem();

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
        $this->transformer = $this->token_transformer;
        $payload = $this->assertRequestPayloadItem();

        // First, make sure consumer_id is valid
        $consumer = null;
        if (!isset($payload['client_id']) or !($consumer = OauthConsumer::fetchByConsumerId($payload['client_id'])))
        {
            return $this->sendErrorUnauthorizedResponse(['message' => 'Oauth consumer not authorized.']);
        }

        // Second, check the grant type
        $grant_type = isset($payload['grant_type']) ? strtolower($payload['grant_type']) : 'none';
        if (!in_array($grant_type, self::$supported_oauth_grant_types))
        {
            return $this->sendErrorBadRequestResponse(['message' => 'Grant type [' . $grant_type . '] not supported.']);
        }

        // Third, create or validate account
        $account = null;
        if ($grant_type == 'new')
        {
            $account = new Account();
            $account->setCreateCode(Account::CREATE_CODE_MOBILE_APP);
            $account->save();

            $profile = new AccountProfile();
            $profile->setAccount($account);
            $profile->save();

        }
        elseif ($grant_type == 'password')
        {
            $account = null;

            if (!isset($payload['username']) or
                !isset($payload['password']) or
                !($account = Account::fetchByEmail($payload['username'])) or
                !$account->isPasswordValid($payload['password']))
            {
                return $this->sendErrorForbiddenResponse(['message' => 'Username and password do not match.']);
            }
        }

        // Fourth, see if a token already exists, else create one
        $access_token = OauthAccessToken::fetchByAccountAndConsumer($account, $consumer);
        if (!$access_token)
        {
            $access_token = new OauthAccessToken();
            $access_token->account_id = $account->id;
            $access_token->oauth_consumer_id = $consumer->id;
            $this->assertValid($access_token);
            $access_token->save();
        }

        // Finally, return the token
        return $this->sendSuccessResponse($access_token);
    }

}
