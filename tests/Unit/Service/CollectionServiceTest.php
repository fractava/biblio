<?php

namespace OCA\Biblio\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Db\DoesNotExistException;

use OCA\Biblio\Service\CollectionService;
use OCA\Biblio\Service\CollectionMemberService;
use OCA\Biblio\Db\Collection;
use OCA\Biblio\Db\CollectionMapper;

class CollectionServiceTest extends TestCase {
	private $service;
	private $mapper;
	private $memberService;
	private $userId = 'tomtester';

	protected function setUp(): void {
		$this->mapper = $this->getMockBuilder(CollectionMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->memberService = $this->getMockBuilder(CollectionMemberService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->service = new CollectionService($this->mapper, $this->memberService);
	}

	public function testUpdate() {
		// the existing collection
		$collection = Collection::fromRow([
			'id' => 3,
			'name' => 'oldName',
			'nomenclatureItem' => 'oldNomenclatureItem',
			'nomenclatureInstance' => 'oldNomenclatureInstance',
			'nomenclatureCustomer' => 'oldNomenclatureCustomer',
			'itemFieldsOrder' => '[1,2,3]',
			'loanFieldsOrder' => '[4,5,6]',
			'customerFieldsOrder' => '[7,8,9]',
		]);
		
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(3))
			->will($this->returnValue($collection));

		// the collection when updated
		$updatedCollection = Collection::fromRow(['id' => 3]);
		$updatedCollection->setName('newName');
		$updatedCollection->setNomenclatureItem('newNomenclatureItem');
		$updatedCollection->setNomenclatureInstance('newNomenclatureInstance');
		$updatedCollection->setNomenclatureCustomer('newNomenclatureCustomer');
		$updatedCollection->setItemFieldsOrder('[1,3,2]');
		$updatedCollection->setLoanFieldsOrder('[4,6,5]');
		$updatedCollection->setCustomerFieldsOrder('[7,9,8]');

		$this->mapper->expects($this->once())
			->method('update')
			->with($this->equalTo($updatedCollection))
			->will($this->returnValue($updatedCollection));

		$result = $this->service->update(
			3,
			'newName',
			'newNomenclatureItem',
			'newNomenclatureInstance',
			'newNomenclatureCustomer',
			'[1,3,2]',
			'[4,6,5]',
			'[7,9,8]',
		);

		$this->assertEquals($updatedCollection, $result);
	}


	/**
	 * @expectedException OCA\Biblio\Service\NotFoundException
	 */
	public function testUpdateNotFound() {
		// test the correct status code if no collection is found
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(3))
			->will($this->throwException(new DoesNotExistException('')));

		$this->service->update(
			3,
			'newName',
			'newNomenclatureItem',
			'newNomenclatureInstance',
			'newNomenclatureCustomer',
			'[1,3,2]',
			'[4,6,5]',
			'[7,9,8]',
		);
	}
}
