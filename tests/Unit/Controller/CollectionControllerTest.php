<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use OCP\AppFramework\Http;
use OCP\IRequest;

use OCA\Biblio\Service\CollectionService;
use OCA\Biblio\Errors\CollectionNotFound;
use OCA\Biblio\Controller\CollectionController;

class CollectionControllerTest extends TestCase {
	protected $controller;
	protected $service;
	protected $userId = 'tomtester';
	protected $request;

	protected function setUp(): void {
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(CollectionService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new CollectionController(
			$this->request, $this->service, $this->userId
		);
	}

	public function testUpdate() {
		$testReturn = 'just check if this value is returned correctly';
		$this->service->expects($this->once())
			->method('update')
			->with($this->equalTo(3),
				$this->equalTo('name'),
				$this->equalTo('nomenclatureItem'),
				$this->equalTo('nomenclatureInstance'),
				$this->equalTo('nomenclatureCustomer'),
				$this->equalTo('itemFieldsOrder'),
				$this->equalTo('loanFieldsOrder'),
				$this->equalTo('customerFieldsOrder'))
			->will($this->returnValue($testReturn));

		$result = $this->controller->update(3, 'name', 'nomenclatureItem', 'nomenclatureInstance', 'nomenclatureCustomer', 'itemFieldsOrder', 'loanFieldsOrder', 'customerFieldsOrder');

		$this->assertEquals($testReturn, $result->getData());
	}


	public function testUpdateNotFound() {
		// test the correct status code if no note is found
		$this->service->expects($this->once())
			->method('update')
			->will($this->throwException(new CollectionNotFound()));

		$result = $this->controller->update(3, 'name', 'nomenclatureItem', 'nomenclatureInstance', 'nomenclatureCustomer', 'itemFieldsOrder', 'loanFieldsOrder', 'customerFieldsOrder');

		$this->assertEquals(Http::STATUS_NOT_FOUND, $result->getStatus());
	}
}
