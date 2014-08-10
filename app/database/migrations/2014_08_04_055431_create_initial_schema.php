<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInitialSchema extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accounts', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('given_name', 50)->nullable();
			$table->string('surname', 50)->nullable();
			$table->string('password', 100)->nullable();
            $table->string('email_address', 100)->nullable();
            $table->string('create_code', 1);
            $table->string('verification_code', 1)->nullable();
            $table->timestamp('verification_timestamp')->nullable();
			$table->timestamps();
            $table->unique('email_address');
		});

//        Schema::create('emails', function(Blueprint $table)
//        {
//            $table->bigIncrements('id');
//            $table->unsignedBigInteger('account_id');
//            $table->boolean('is_primary')->default(false);
//            $table->string('address', 100);
//            $table->string('verification_code', 1)->nullable();
//            $table->timestamp('verification_timestamp');
//            $table->timestamps();
//            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
//            $table->unique('address');
//        });

        Schema::create('oauth_consumers', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('description', 255);
            $table->string('consumer_id', 100);
            $table->string('consumer_secret', 100);
            $table->string('type', 1)->default('M');
            $table->timestamps();
            $table->unique('consumer_id');
        });

        Schema::create('oauth_access_tokens', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('oauth_consumer_id');
            $table->string('token', 100);
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('oauth_consumer_id')->references('id')->on('oauth_consumers')->onDelete('cascade');
            $table->unique('token');
        });

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('oauth_access_tokens');
        Schema::drop('oauth_consumers');
//        Schema::drop('emails');
        Schema::drop('accounts');
	}

}
