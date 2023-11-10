<?php

namespace OCA\Biblio\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\IDBConnection;

use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Service\ItemFieldValueService;
use OCA\Biblio\Service\HistoryEntryService;
use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemMapper;
use OCA\Biblio\Errors\ItemNotFound;

#[CoversClass(ItemService::class)]
class ItemServiceTest extends TestCase {
	private $service;
	private $mapper;
	private $itemFieldValueService;
	private $historyEntryService;
	private $db;
	private $userId = 'tomtester';

	protected function setUp(): void {
		$this->mapper = $this->getMockBuilder(ItemMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->itemFieldValueService = $this->getMockBuilder(ItemFieldValueService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->historyEntryService = $this->getMockBuilder(HistoryEntryService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->db = $this->getMockBuilder(IDBConnection::class)
			->disableOriginalConstructor()
			->getMock();
		$this->service = new ItemService($this->mapper, $this->itemFieldValueService, $this->historyEntryService, $this->db);
	}

	public function testUpdate() {
		// the existing item
		$item = Item::fromRow([
			'id' => 3,
			'collectionId' => 1,
			'title' => 'oldTitle',
		]);
		
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(1),
				$this->equalTo(3))
			->will($this->returnValue($item));

		// the item when updated
		$updatedItem = Item::fromRow([
			'id' => 3,
			'collectionId' => 1,
		]);
		$updatedItem->setTitle('newTitle');

		$this->mapper->expects($this->once())
			->method('update')
			->with($this->equalTo($updatedItem))
			->will($this->returnValue($updatedItem));

		$result = $this->service->update(
			1,
			3,
			'newTitle',
		);

		$this->assertEquals($updatedItem, $result);
	}

	public function testUpdateNotFound() {
		// test if the correct error is thrown, if an item is not found
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(1),
				$this->equalTo(3))
			->will($this->throwException(new DoesNotExistException('')));

		$this->expectException(ItemNotFound::class);

		$this->service->update(
			1,
			3,
			'newTitle',
		);
	}

	public function testDelete() {
		$item = Item::fromRow([
			'id' => 3,
			'collectionId' => 1,
			'title' => 'test title',
		]);
		
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(1),
				$this->equalTo(3))
			->will($this->returnValue($item));

		$this->mapper->expects($this->once())
			->method('delete')
			->with($this->equalTo($item))
			->will($this->returnValue($item));

		$result = $this->service->delete(1, 3);

		$this->assertEquals($item, $result);
	}
}
