<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\ItemFieldValue;
use OCA\Biblio\Db\ItemFieldValueMapper;

class ItemFieldValueService {

	/** @var ItemFieldValueMapper */
	private $mapper;

	public function __construct(ItemFieldValueMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $itemId): array {
		return $this->mapper->findAll($itemId);
	}

	public function findAllIncludingFields(int $collectionId, int $itemId, bool $onlyIncludedInList = false) {
		return $this->mapper->findAllIncludingFields($collectionId, $itemId, $onlyIncludedInList);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id): ItemFieldValue {
		try {
			return $this->mapper->find($id);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function findByItemAndFieldId(int $itemId, int $fieldId): ItemFieldValue {
		try {
			return $this->mapper->findByItemAndFieldId($itemId, $fieldId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function findByItemAndFieldIdIncludingField(int $collectionId, int $itemId, int $fieldId) {
		try {
			return $this->mapper->findByItemAndFieldIdIncludingField($collectionId, $itemId, $fieldId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $itemId, int $fieldId, string $value): ItemFieldValue {
		$fieldValue = new ItemFieldValue();
        $fieldValue->setItemId($itemId);
		$fieldValue->setFieldId($fieldId);
		$fieldValue->setValue($value);
		return $this->mapper->insert($fieldValue);
	}

	public function update(int $id, string $newValue): ItemFieldValue {
		try {
			$fieldValue = $this->mapper->find($id);
			
			if (!is_null($newValue)) {
				$fieldValue->setValue($newValue);
			}

			return $this->mapper->update($fieldValue);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function updateByItemAndFieldId(int $itemId, int $fieldId, string $newValue): ItemFieldValue {
		if (!is_null($newValue)) {
			try {
				$fieldValue = $this->mapper->findByItemAndFieldId($itemId, $fieldId);
				$fieldValue->setValue($newValue);
				return $this->mapper->update($fieldValue);
			} catch ( DoesNotExistException $e ) {
				return $this->create($itemId, $fieldId, $newValue);
			} catch ( \Exception $e ) {
				$this->handleException($e);
			}
		}
	}

	public function delete($id): ItemFieldValue {
		try {
			$fieldValue = $this->mapper->find($id);
			$this->mapper->delete($fieldValue);
			return $fieldValue;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function deleteByItemAndFieldId(int $itemId, int $fieldId): ItemFieldValue {
		try {
			$fieldValue = $this->mapper->findByItemAndFieldId($itemId, $fieldId);
			$this->mapper->delete($fieldValue);
			return $fieldValue;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
