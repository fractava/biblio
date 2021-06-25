<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Http;
use OCP\IRequest;

use OCA\Biblio\Service\MediumNotFound;
use OCA\Biblio\Service\MediumService;
use OCA\Biblio\Controller\MediumController;

class MediumControllerTest extends TestCase {
	protected $controller;
	protected $service;
	protected $userId = 'john';
	protected $request;

	public function setUp(): void {
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(MediumService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new MediumController($this->request, $this->service, $this->userId);
	}

	public function testUpdate() {
		$medium = 'just check if this value is returned correctly';
		$this->service->expects($this->once())
			->method('update')
			->with($this->equalTo(3),
					$this->equalTo('title'),
					$this->equalTo('data'),
				   $this->equalTo($this->userId))
			->will($this->returnValue($medium));

		$result = $this->controller->update(3, 'title', 'data');

		$this->assertEquals($medium, $result->getData());
	}


	public function testUpdateNotFound() {
		// test the correct status code if no medium is found
		$this->service->expects($this->once())
			->method('update')
			->will($this->throwException(new MediumNotFound()));

		$result = $this->controller->update(3, 'title', 'data');

		$this->assertEquals(Http::STATUS_NOT_FOUND, $result->getStatus());
	}
}
