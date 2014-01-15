<?php

class HomeTest extends TestCase {

	public function testForHome() {
		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
	}

}