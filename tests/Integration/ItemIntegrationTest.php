<?php

namespace OCA\Biblio\Tests\Integration;

use OCP\AppFramework\App;
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
	}

	public function testUpdate() {
		// prepare collection and collection membership
		$collection = $this->collectionService->create("Test Collection", "[]", "[]", "[]", $this->userId);
		$collectionId = $collection->getId();

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
		$this->collectionService->delete($collectionId);
	}
}
