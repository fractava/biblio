<?php

namespace OCA\Biblio\Tests\Integration;

use OCP\AppFramework\App;
use OCP\AppFramework\Http;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemMapper;
use OCA\Biblio\Controller\ItemController;
use OCA\Biblio\Service\CollectionService;

class ItemIntegrationTest extends TestCase {
	private $controller;
	private $mapper;
	private $collectionService;
	private $userId = 'tomtester';

	private $collection;

	public function setUp(): void {
		$app = new App('biblio');
		$container = $app->getContainer();

		// only replace the user id
		$container->registerService('userId', function () {
			return $this->userId;
		});

		// we do not care about the request but the controller needs it
		$container->registerService(IRequest::class, function () {
			return $this->createMock(IRequest::class);
		});

		$this->controller = $container->query(ItemController::class);
		$this->mapper = $container->query(ItemMapper::class);
		$this->collectionService = $container->query(CollectionService::class);

		// prepare collection and collection membership
		$this->collection = $this->collectionService->create("Test Collection", "[]", "[]", "[]", $this->userId);
	}

	protected function tearDown() {
		$this->collectionService->delete($this->collection->getId());
		unset($this->collection);
	}

	public function testCreateAndDestroy() {
		$collectionId = $this->collection->getId();

		// create new item
		$itemCreateData = $this->controller->create($collectionId, 'Item Title')->getData();
		$itemId = $itemCreateData["id"];

		// test if database entry got created correctly
		$this->assertNotNull($itemId);
		$this->assertEquals($collectionId, $itemCreateData["collectionId"]);
		$this->assertEquals('Item Title', $itemCreateData["title"]);
		$this->assertEquals([], $itemCreateData["fieldValues"]);

		// destroy item
		$itemDestroyData = $this->controller->destroy($collectionId, $itemId)->getData();
		$this->assertEquals($itemId, $itemDestroyData["id"]);
		$this->assertEquals($collectionId, $itemDestroyData["collectionId"]);
		$this->assertEquals('Item Title', $itemDestroyData["title"]);

		// assure item can no longer be found
		$itemShowResultAfterDestroy = $this->controller->show($collectionId, $itemId, "model+fields");
		$this->assertEquals(Http::STATUS_NOT_FOUND, $itemShowResultAfterDestroy->getStatus());
	}

	public function testUpdate() {
		$collectionId = $this->collection->getId();

		// create a new item that should be updated
		$item = new Item();
		$item->setCollectionId($collectionId);
		$item->setTitle('Test Item');

		$id = $this->mapper->insert($item)->getId();

		// fromRow does not set the fields as updated
		$updatedItem = Item::fromRow([
			'id' => $id,
			'collectionId' => $collectionId,
		]);
		$updatedItem->setTitle('New Item Title');

		$result = $this->controller->update($collectionId, $id, 'New Item Title');

		$this->assertEquals($updatedItem, $result->getData());

		// clean up
		$this->mapper->delete($result->getData());
	}
}
