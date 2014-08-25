<?php

use Illuminate\Http\Response as HttpResponse;

class OuathTest extends ApiTestCase {

    public function testHealthCheck()
    {
        $response = $this->getResponse('GET', '/');
        $this->assertEquals(HttpResponse::HTTP_OK, $response->getStatusCode());
        $this->assertContains('status', $response->getContent());

        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals('ok', $responseJson['status']);
    }

    public function testConsumerCreationWithBadPassword()
    {
        $response = $this->getResponse('POST', '/v1/oauth_consumer', '',
            ['item' => [
                'name' => 'Test Consumer',
                'description' => 'A test consumer...',
                'authority' => 'not authority'
            ]]
        );

        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertContains('hello', $response->getContent());
    }

    public function testConsumerCreationWithCorrectPassword()
    {
        $response = $this->getResponse('POST', '/v1/oauth_consumer', '',
            ['item' => [
                'name' => 'Test Consumer',
                'description' => 'A test consumer...',
                'authority' => 'olive baboon'
            ]]
        );

        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

}
