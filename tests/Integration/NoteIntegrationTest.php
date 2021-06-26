<?php

namespace OCA\Biblio\Tests\Integration\Controller;

use OCP\AppFramework\App;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;


use OCA\Biblio\Db\Medium;
use OCA\Biblio\Db\MediumMapper;
use OCA\Biblio\Controller\MediumController;

class MediumIntegrationTest extends TestCase {
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

		$this->controller = $container->query(MediumController::class);
		$this->mapper = $container->query(MediumMapper::class);
	}

	public function testUpdate() {
		// create a new medium that should be updated
		$medium = new Medium();
		$medium->setTitle('old_title');
		$medium->setFields('old_fields');
		$medium->setUserId($this->userId);

		$id = $this->mapper->insert($medium)->getId();

		// fromRow does not set the fields as updated
		$updatedMedium = Medium::fromRow([
			'id' => $id,
			'user_id' => $this->userId
		]);
		$updatedMedium->setFields('fields');
		$updatedMedium->setTitle('title');

		$result = $this->controller->update($id, 'title', 'fields');

		$this->assertEquals($updatedMedium, $result->getFields());

		// clean up
		$this->mapper->delete($result->getFields());
	}
}
