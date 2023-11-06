<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use OCP\AppFramework\Http;
use OCP\IRequest;

use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Errors\ItemNotFound;
use OCA\Biblio\Controller\ItemController;

class ItemControllerTest extends TestCase {
	protected $controller;
	protected $service;
	protected $userId = 'tomtester';
	protected $request;

	protected function setUp(): void {
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(ItemService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new ItemController(
			$this->request, $this->service, $this->userId
		);
	}

	public function testUpdate() {
		$testReturn = 'just check if this value is returned correctly';
		
		$this->service->expects($this->once())
			->method('update')
			->with($this->equalTo(1),
				$this->equalTo(3),
				$this->equalTo('title'))
			->will($this->returnValue($testReturn));
		
		$result = $this->controller->update(1, 3, 'title');

		$this->assertEquals($testReturn, $result->getData());
	}


	public function testUpdateNotFound() {
		// test if the correct status code is returned if no item is found
		$this->service->expects($this->once())
			->method('update')
			->will($this->throwException(new ItemNotFound()));

		$result = $this->controller->update(1, 3, 'title');

		$this->assertEquals(Http::STATUS_NOT_FOUND, $result->getStatus());
	}
}
