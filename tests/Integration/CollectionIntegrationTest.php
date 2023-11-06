<?php

namespace OCA\Biblio\Tests\Integration;

use OCP\AppFramework\App;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;

use OCA\Biblio\Db\Collection;
use OCA\Biblio\Db\CollectionMapper;
use OCA\Biblio\Controller\CollectionController;

class CollectionIntegrationTest extends TestCase {
	private $controller;
	private $mapper;
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

		$this->controller = $container->query(CollectionController::class);
		$this->mapper = $container->query(CollectionMapper::class);
	}

	public function testUpdate() {
		// create a new collection that should be updated
		$collection = new Collection();
		$collection->setName('oldName');
		$collection->setNomenclatureItem('oldNomenclatureItem');
		$collection->setNomenclatureInstance('oldNomenclatureInstance');
		$collection->setNomenclatureCustomer('oldNomenclatureCustomer');
		$collection->setItemFieldsOrder('[1,2,3]');
		$collection->setLoanFieldsOrder('[4,5,6]');
		$collection->setCustomerFieldsOrder('[7,8,9]');

		$id = $this->mapper->insert($collection)->getId();

		// fromRow does not set the fields as updated
		$updatedCollection = Collection::fromRow(['id' => $id]);
		$updatedCollection->setName('newName');
		$updatedCollection->setNomenclatureItem('newNomenclatureItem');
		$updatedCollection->setNomenclatureInstance('newNomenclatureInstance');
		$updatedCollection->setNomenclatureCustomer('newNomenclatureCustomer');
		$updatedCollection->setItemFieldsOrder('[1,3,2]');
		$updatedCollection->setLoanFieldsOrder('[4,6,5]');
		$updatedCollection->setCustomerFieldsOrder('[7,9,8]');

		$result = $this->controller->update(
			$id,
			'newName',
			'newNomenclatureItem',
			'newNomenclatureInstance',
			'newNomenclatureCustomer',
			'[1,3,2]',
			'[4,6,5]',
			'[7,9,8]',
		);

		$this->assertEquals($updatedCollection, $result->getData());

		// clean up
		$this->mapper->delete($result->getData());
	}
}
