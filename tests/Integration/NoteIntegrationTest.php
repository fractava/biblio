<?php

namespace OCA\Biblio\Tests\Integration\Controller;

use OCP\AppFramework\App;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;


use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemMapper;
use OCA\Biblio\Controller\ItemController;

class ItemIntegrationTest extends TestCase {
	private $controller;
	private $mapper;
	private $userId = 'john';

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
	}

	public function testUpdate() {
		// create a new item that should be updated
		$item = new Item();
		$item->setTitle('old_title');
		$item->setFields('old_fields');
		$item->setUserId($this->userId);

		$id = $this->mapper->insert($item)->getId();

		// fromRow does not set the fields as updated
		$updatedItem = Item::fromRow([
			'id' => $id,
			'userId' => $this->userId
		]);
		$updatedItem->setFields('fields');
		$updatedItem->setTitle('title');

		$result = $this->controller->update($id, 'title', 'fields');

		$this->assertEquals($updatedItem, $result->getFields());

		// clean up
		$this->mapper->delete($result->getFields());
	}
}
