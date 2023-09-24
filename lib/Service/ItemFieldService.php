<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Collection;
use OCA\Biblio\Db\ItemField;
use OCA\Biblio\Db\ItemFieldMapper;

class ItemFieldService {

	/** @var ItemFieldMapper */
	private $mapper;

	public function __construct(ItemFieldMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $collectionId): array {
		return $this->mapper->findAll($collectionId);
	}

	public function findAllInOrder(Collection $entity): array {
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

	public function find(int $id, int $collectionId): ItemField {
		try {
			return $this->mapper->find($id, $collectionId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $type, string $name, string $settings, bool $includeInList = false): ItemField {
		$field = new ItemField();
        $field->setCollectionId($collectionId);
		$field->setType($type);
		$field->setName($name);
		$field->setSettings($settings);
		$field->setIncludeInList((int)$includeInList);
		return $this->mapper->insert($field);
	}

	public function update(int $id, int $collectionId, string $newType, string $newName, string $newSettings, bool $newIncludeInList): ItemField {
		try {
			$field = $this->mapper->find($id, $collectionId);
			
			if (!is_null($newType)) {
				$field->setType($newType);
			}
			if (!is_null($newName)) {
				$field->setName($newName);
			}
			if (!is_null($newSettings)) {
				$field->setSettings($newSettings);
			}
			if (!is_null($newIncludeInList)) {
				$field->setIncludeInList((int)$newIncludeInList);
			}

			return $this->mapper->update($field);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete($id, $collectionId): ItemField {
		try {
			$field = $this->mapper->find($id, $collectionId);
			$this->mapper->delete($field);
			return $field;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
