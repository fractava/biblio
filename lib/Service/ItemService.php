<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemMapper;

use OCA\Biblio\Service\ItemFieldValueService;

class ItemService {

	/** @var ItemMapper */
	private $mapper;

	/** @var ItemFieldValueService */
	private $fieldValueService;

	public function __construct(ItemMapper $mapper, ItemFieldValueService $fieldValueService) {
		$this->mapper = $mapper;
		$this->fieldValueService = $fieldValueService;
	}

	public function findAll(int $collectionId): array {
		return $this->mapper->findAll($collectionId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id, int $collectionId) {
		try {
			return $this->mapper->find($id, $collectionId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $title, /*array $fields*/) {
		$item = new Item();
		$item->setCollectionId($collectionId);
		$item->setTitle($title);

		$item = $this->mapper->insert($item);

		/*if(sizeof($fields) > 0){
			$itemId = $item->getId();

			foreach($fields as $field) {
				// TODO: Check if fieldId is in same collection as item

				$fieldEntity = $this->fieldValueService->create($itemId, $field["fieldId"], $field["value"]);
			}
		}*/

		return $item;
	}

	public function update(int $id, int $collectionId, string $newTitle) {
		try {
			$item = $this->mapper->find($id, $collectionId);
			
			if (!is_null($title)) {
				$item->setTitle($title);
			}

			return $this->mapper->update($item);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $id, int $collectionId) {
		try {
			$item = $this->mapper->find($id, $collectionId);
			$this->mapper->delete($item);
			return $item;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
