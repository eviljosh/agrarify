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

    public function testConsumerCreation()
    {
        
    }

}
