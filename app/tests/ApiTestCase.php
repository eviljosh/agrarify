<?php

use Faker\Factory as FakerFactory;

class ApiTestCase extends TestCase {

    /**
     * @var Faker\Generator
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

}
