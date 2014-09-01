<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddVeggies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('veggies', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('location_id'); // no foreign key here; relationship managed through business logic
            $table->unsignedBigInteger('availability_id')->nullable(); // no foreign key here; relationship managed through business logic
            $table->tinyInteger('status');
            $table->smallInteger('type');
            $table->tinyInteger('freshness');
            $table->smallInteger('quantity');
            $table->text('notes');
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
		});

        Schema::create('agrarify_messages', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('recipient_id');
            $table->smallInteger('type');
            $table->unsignedBigInteger('other_id')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->index('other_id');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('veggies');
        Schema::drop('agrarify_messages');
	}

}
