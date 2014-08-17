<?php

class OuathTest extends ApiTestCase {

    public function testConsumerCreation()
    {

    }


    /**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
	}

}
