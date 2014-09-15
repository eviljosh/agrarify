<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePushRegistrations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('push_registrations', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->string('token');
            $table->char('type', 1);
            $table->string('device_name')->nullable();
            $table->string('sns_arn')->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('push_registrations');
	}

}
