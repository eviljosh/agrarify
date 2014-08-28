<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveAccountAddressesAndModifyLocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::drop('account_addresses');

        Schema::table('locations', function(Blueprint $table)
        {
            $table->unsignedBigInteger('account_id');
            $table->boolean('is_primary')->default(false);
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
        Schema::table('locations', function(Blueprint $table)
        {
            $table->dropForeign('locations_account_id_foreign');
            $table->dropColumn(['account_id', 'is_primary']);
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

}
