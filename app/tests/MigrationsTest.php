<?php

class MigrationTest extends TestCase {
	
	public function testMigrations() {
		// If anything goes wrong there will be an expection.
		Artisan::call('migrate');
		Artisan::call('migrate:reset');
	}
}