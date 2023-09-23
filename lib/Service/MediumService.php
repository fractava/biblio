<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemMapper;

use OCA\Biblio\Service\ItemFieldService;

class ItemService {

	/** @var ItemMapper */
	private $mapper;

	/** @var ItemFieldService */
	private $fieldService;

	public function __construct(ItemMapper $mapper, ItemFieldService $fieldService) {
		$this->mapper = $mapper;
		$this->fieldService = $fieldService;
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

	public function create(string $title, array $fields, int $collectionId) {
		$item = new Item();
		$item->setTitle($title);

		$fieldsOrder = [];

		$item->setFieldsOrder($fieldsOrder);
		$item->setCollectionId($collectionId);

		$item = $this->mapper->insert($item);

		if(sizeof($fields) > 0){
			$itemId = $item->getId();

			foreach($fields as $field) {
				$fieldEntity = $this->fieldService->create($itemId, $field["type"], $field["title"], $field["value"]);
				$fieldsOrder[] = $fieldEntity->getId();
			}

			$item->setFieldsOrder(json_encode($fieldsOrder));

			$item = $this->mapper->update($item);
		}

		return $item;
	}

	public function update(int $id, int $collectionId, string $title, $fieldsOrder) {
		try {
			$item = $this->mapper->find($id, $collectionId);
			
			if (!is_null($title)) {
				$item->setTitle($title);
			}
			if (!is_null($fieldsOrder)) {
				$item->setFieldsOrder($fieldsOrder);
			}

			return $this->mapper->update($item);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $id, int $collectionId) {
		try {
			$item = $this->mapper->find($id, $collectionId);
			$this->mapper->delete($itemj);
			return $item;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
