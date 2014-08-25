<?php

use Faker\Factory as FakerFactory;

class ApiTestCase extends \Agrarify\Api\Tests\TestCase {

    /**
     * @var Faker\Generator
     */
    protected $faker;

    public function __construct()
    {
        parent::__construct();
        $this->faker = FakerFactory::create();
    }

    public function createApplication()
    {
        $val = parent::createApplication();
        $this->setupDatabase();
        return $val;
    }

    protected function setupDatabase()
    {
    \Illuminate\Support\Facades\Artisan::call('migrate');
    //Artisan::call('db:seed');
    $this->migrated = true;
    }

    public function testNothing()
    {

    }

    protected function getResponse($verb, $url, $token = '', $payload = [])
    {
        return $this->call($verb, $url, [], [], ['Authorization' => 'Bearer ' . $token], json_encode($payload));
    }

}
