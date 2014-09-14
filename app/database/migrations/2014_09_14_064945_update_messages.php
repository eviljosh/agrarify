<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateMessages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('agrarify_messages', function(Blueprint $table)
		{
			$table->boolean('read_by_recipient')->default(false);
            $table->boolean('ignored_by_recipient')->default(false);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('agrarify_messages', function(Blueprint $table)
		{
            $table->dropColumn(['read_by_recipient', 'ignored_by_recipient']);
		});
	}

}
