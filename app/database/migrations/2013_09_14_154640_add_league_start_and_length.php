<?php

use Illuminate\Database\Migrations\Migration;

class AddLeagueStartAndLength extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leagues', function($table) {
			$table->date('start_date')->after('units');
			$table->integer('extra_weeks')->after('units');
		});
		DB::table('leagues')->update(array('extra_weeks' => 4, 'start_date' => '2013-04-19'));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leagues', function($table) {
			$table->dropColumn('extra_weeks', 'start_date');
		});
	}

}