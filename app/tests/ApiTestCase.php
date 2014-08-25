<?php

use Faker\Factory as FakerFactory;

use Illuminate\Http\Response as HttpResponse;

use Agrarify\Api\Exception\ApiErrorException;
use Agrarify\Models\Accounts\Account;
use Agrarify\Models\Oauth2\OauthConsumer;
use Agrarify\Models\Oauth2\OauthAccessToken;

class ApiTestCase extends \Agrarify\Api\Tests\TestCase {

    const ACCESS_TOKEN_1 = '12345abcde';
    const EMAIL_ADDRESS_1 = 'sue@tester.com';

    /**
     * @var Faker\Generator
     */
    protected $faker;

    public function __construct()
    {
        parent::__construct();
        $this->faker = FakerFactory::create();
    }

    public function setUp()
    {
        parent::setUp();
        $this->setupDatabase();
    }

    protected function setupDatabase()
    {
        Artisan::call('migrate');

        $consumer = new OauthConsumer([
            'name' => 'Test Consumer',
            'description' => 'Test Consumer description'
        ]);
        $consumer->save();

        $account = new Account([
            'given_name' => 'Sue',
            'surname' => 'Tester',
            'email_address' => self::EMAIL_ADDRESS_1
        ]);
        $account->create_code = Account::CREATE_CODE_MOBILE_APP;
        $account->save();

        $token = new OauthAccessToken();
        $token->setAccount($account);
        $token->setOauthConsumer($consumer);
        $token->save();
    }

    public function testNothing()
    {

    }

    protected function getResponse($verb, $url, $token = '', $payload = [])
    {
        try {
            return $this->callSecure($verb, $url, [], [],
                ['Authorization' => 'Bearer ' . $token, 'HTTP_CONTENT_TYPE' => 'application/json'],
                json_encode($payload)
            );
        } catch (ApiErrorException $e) {
            $response = new HttpResponse();
            $response->setStatusCode($e->getHttpStatusCode());
            $response->setContent($e->getErrors());
            return $response;
        }

    }

}
