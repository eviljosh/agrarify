<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubResources extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_profiles', function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->string('profile_slug', 100);
            $table->string('display_name', 100)->nullable();
            $table->text('bio')->nullable();
            $table->string('favorite_veggie', 50)->nullable();
            $table->boolean('is_interested_in_getting_veggies')->default(false);
            $table->boolean('is_interested_in_giving_veggies')->default(false);
            $table->boolean('is_interested_in_gardening')->default(false);
            $table->boolean('is_interested_in_providing_gardens')->default(false);
			$table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->unique('profile_slug');
		});

        Schema::create('locations', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('nick_name', 100)->nullable();
            $table->string('number', 20)->nullable();
            $table->string('street', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('postal_code', 15)->nullable();
            $table->string('latitude', 12)->nullable();
            $table->string('longitude', 12)->nullable();
            $table->string('geohash', 12)->nullable();
            $table->timestamps();
            $table->index('geohash');
        });

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

        Schema::create('account_addresses', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('location_id');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
	}

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('account_profiles');
        Schema::drop('locations');
        Schema::drop('availabilities');
        Schema::drop('account_addresses');
	}

}
