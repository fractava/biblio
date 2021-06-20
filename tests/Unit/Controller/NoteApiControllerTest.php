<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use OCA\Biblio\Controller\BiblioApiController;

class BiblioApiControllerTest extends BiblioControllerTest {
	public function setUp(): void {
		parent::setUp();
		$this->controller = new BiblioApiController($this->request, $this->service, $this->userId);
	}
}
