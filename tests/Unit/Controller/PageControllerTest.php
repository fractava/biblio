<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use OCP\AppFramework\Http\TemplateResponse;

use OCA\Biblio\Controller\PageController;

#[CoversClass(PageController::class)]
class PageControllerTest extends TestCase {
	private $controller;

	public function setUp(): void {
		$request = $this->getMockBuilder('OCP\IRequest')->getMock();
		$this->controller = new PageController($request);
	}


	public function testIndex() {
		$result = $this->controller->index();

		$this->assertEquals('main', $result->getTemplateName());
		$this->assertTrue($result instanceof TemplateResponse);
	}
}
