<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemField;
use OCA\Biblio\Db\ItemFieldMapper;

class ItemFieldService {

	/** @var ItemFieldMapper */
	private $mapper;

	public function __construct(ItemFieldMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $itemId): array {
		return $this->mapper->findAll($itemId);
	}

	public function findAllInOrder(Item $entity): array {
		$fieldsOrder = json_decode($entity->getFieldsOrder(), true) ?: [];
		$fields = $this->findAll($entity->getId());

		$fieldsFiltered = [];
		foreach($fields as $field) {
			if(in_array($field->getId(), $fieldsOrder)) {
				$fieldsFiltered[] = $field;
			}
		}

		$cmp = function (ItemField $a, ItemField $b) use ($fieldsOrder): int {
			$pos1 = array_search($a->getId(), $fieldsOrder);
   			$pos2 = array_search($b->getId(), $fieldsOrder);

			if($pos1==$pos2) {
			   return 0;
			} else {
				return ($pos1 < $pos2 ? -1 : 1);
			}
		};

		usort($fieldsFiltered, $cmp);

		return $fieldsFiltered;
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id, int $itemId) {
		try {
			return $this->mapper->find($id, $itemId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create($itemId, $type, $title, $value) {
		$field = new ItemField();
        $field->setItemId($itemId);
		$field->setType($type);
		$field->setTitle($title);
		$field->setValue($value);
		return $this->mapper->insert($field);
	}

	public function update($id, $itemId, $type, $title, $value) {
		try {
			$field = $this->mapper->find($id, $itemId);
			
			if (!is_null($type)) {
				$field->setType($type);
			}
			if (!is_null($title)) {
				$field->setTitle($title);
			}
			if (!is_null($value)) {
				$field->setValue($value);
			}

			return $this->mapper->update($field);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete($id, $itemId) {
		try {
			$field = $this->mapper->find($id, $itemId);
			$this->mapper->delete($field);
			return $field;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function findUniqueTitles() {
		return $this->mapper->findUniqueTitles();
	}
}
