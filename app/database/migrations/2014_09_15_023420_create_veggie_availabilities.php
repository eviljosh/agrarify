<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVeggieAvailabilities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('veggie_availabilities', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->unsignedBigInteger('veggie_id');
            $table->smallInteger('type');
            $table->date('availability_date');
            $table->tinyInteger('start_hour')->nullable();
            $table->tinyInteger('end_hour')->nullable();
            $table->timestamps();
            $table->foreign('veggie_id')->references('id')->on('veggies')->onDelete('cascade');
		});

        Schema::table('veggies', function(Blueprint $table)
        {
            $table->dropColumn(['availability_id']);
        });

        Schema::drop('availabilities');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('veggie_availabilities');

        Schema::create('availabilities', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->smallInteger('type');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->tinyInteger('start_hour')->nullable();
            $table->tinyInteger('end_hour')->nullable();
            $table->boolean('monday')->default(false);
            $table->boolean('tuesday')->default(false);
            $table->boolean('wednesday')->default(false);
            $table->boolean('thursday')->default(false);
            $table->boolean('friday')->default(false);
            $table->boolean('saturday')->default(false);
            $table->boolean('sunday')->default(false);
            $table->timestamps();
        });

        Schema::table('veggies', function(Blueprint $table)
        {
            $table->unsignedBigInteger('availability_id')->nullable(); // no foreign key here; relationship managed through business logic
        });
	}

}
