<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddVeggieImages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('veggie_images', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->unsignedBigInteger('veggie_id');
            $table->boolean('is_primary')->default(false);
            $table->string('guid');
            $table->timestamps();
            $table->foreign('veggie_id')->references('id')->on('veggies')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('veggie_images');
	}

}
