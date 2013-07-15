<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LeaguePrivateAndFeatured extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leagues', function(Blueprint $table)
		{
			$table->boolean('private')->default(false)->after('end_date');
			$table->boolean('featured')->default(false)->after('private');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leagues', function($table)
		{
			$table->dropColumn('private', 'featured');
		});
	}

}