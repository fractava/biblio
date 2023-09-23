<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Http;
use OCP\IRequest;

use OCA\Biblio\Service\ItemNotFound;
use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Controller\ItemController;

class ItemControllerTest extends TestCase {
	protected $controller;
	protected $service;
	protected $userId = 'john';
	protected $request;

	public function setUp(): void {
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(ItemService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new ItemController($this->request, $this->service, $this->userId);
	}

	public function testUpdate() {
		$item = 'just check if this value is returned correctly';
		$this->service->expects($this->once())
			->method('update')
			->with($this->equalTo(3),
					$this->equalTo('title'),
					$this->equalTo('fields'),
				   $this->equalTo($this->userId))
			->will($this->returnValue($item));

		$result = $this->controller->update(3, 'title', 'fields');

		$this->assertEquals($item, $result->getFields());
	}


	public function testUpdateNotFound() {
		// test the correct status code if no item is found
		$this->service->expects($this->once())
			->method('update')
			->will($this->throwException(new ItemNotFound()));

		$result = $this->controller->update(3, 'title', 'fields');

		$this->assertEquals(Http::STATUS_NOT_FOUND, $result->getStatus());
	}
}
