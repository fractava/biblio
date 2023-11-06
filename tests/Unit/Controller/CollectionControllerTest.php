<?php

namespace OCA\Biblio\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Http;

use OCA\Biblio\Errors\CollectionNotFound;

class CollectionControllerTest extends TestCase {
	protected $controller;
	protected $service;
	protected $userId = 'tomtester';
	protected $request;

	public function setUp() {
		$this->request = $this->getMockBuilder(\OCP\IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(\OCA\Biblio\Service\CollectionService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new \OCA\Biblio\Controller\CollectionController(
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

		$result = $this->controller->update(3, 'name', 'nomenclatureItem', 'nomenclatureInstance', 'nomenclatureCustomer', 'itemFieldsOrder', 'itemFieldsOrder', 'loanFieldsOrder', 'customerFieldsOrder');

		$this->assertEquals($testReturn, $result->getData());
	}


	public function testUpdateNotFound() {
		// test the correct status code if no note is found
		$this->service->expects($this->once())
			->method('update')
			->will($this->throwException(new CollectionNotFound()));

		$result = $this->controller->update(3, 'name', 'nomenclatureItem', 'nomenclatureInstance', 'nomenclatureCustomer', 'itemFieldsOrder', 'itemFieldsOrder', 'loanFieldsOrder', 'customerFieldsOrder');

		$this->assertEquals(Http::STATUS_NOT_FOUND, $result->getStatus());
	}
}
