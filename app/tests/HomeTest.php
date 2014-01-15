<?php

class HomeTest extends TestCase {

	public function setUp() {
		parent::setUp();

		Artisan::call('migrate');
		$this->seed();
	}

	public function testForHome() {
		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
	}

}