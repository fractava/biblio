<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use OCA\Biblio\Controller\ItemApiController;

class BiblioApiControllerTest extends ItemControllerTest {
	public function setUp(): void {
		parent::setUp();
		$this->controller = new ItemApiController($this->request, $this->service, $this->userId);
	}
}
