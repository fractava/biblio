<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use OCA\Biblio\Controller\MediumApiController;

class BiblioApiControllerTest extends MediumControllerTest {
	public function setUp(): void {
		parent::setUp();
		$this->controller = new MediumApiController($this->request, $this->service, $this->userId);
	}
}
