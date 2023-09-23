<?php

namespace OCA\Biblio\Tests\Unit\Service;

use OCA\Biblio\Service\ItemNotFound;
use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Db\DoesNotExistException;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Db\ItemMapper;

class ItemServiceTest extends TestCase {
	private $service;
	private $mapper;
	private $userId = 'john';

	public function setUp(): void {
		$this->mapper = $this->getMockBuilder(ItemMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->service = new ItemService($this->mapper);
	}

	public function testUpdate() {
		// the existing item
		$item = Item::fromRow([
			'id' => 3,
			'title' => 'yo',
			'fields' => 'nope'
		]);
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(3))
			->will($this->returnValue($item));

		// the item when updated
		$updatedItem = Item::fromRow(['id' => 3]);
		$updatedItem->setTitle('title');
		$updatedItem->setFields('fields');
		$this->mapper->expects($this->once())
			->method('update')
			->with($this->equalTo($updatedItem))
			->will($this->returnValue($updatedItem));

		$result = $this->service->update(3, 'title', 'fields', $this->userId);

		$this->assertEquals($updatedItem, $result);
	}

	public function testUpdateNotFound() {
		$this->expectException(ItemNotFound::class);
		// test the correct status code if no item is found
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(3))
			->will($this->throwException(new DoesNotExistException('')));

		$this->service->update(3, 'title', 'fields', $this->userId);
	}
}
