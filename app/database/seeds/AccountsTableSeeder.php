<?php

use Agrarify\Models\Accounts\Account;
use Faker\Factory as Faker;

class AccountsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 10) as $index)
		{
			Account::create([

			]);
		}
	}

}