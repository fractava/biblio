<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\CustomerFieldNotFound;

use OCA\Biblio\Db\CustomerField;
use OCA\Biblio\Db\CustomerFieldMapper;

class CustomerFieldService {
	/** @var CustomerFieldMapper */
	private $mapper;

	public function __construct(CustomerFieldMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $collectionId): array {
		return $this->mapper->findAll($collectionId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new CustomerFieldNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id, int $collectionId): CustomerField {
		try {
			return $this->mapper->find($id, $collectionId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $type, string $name, string $settings, bool $includeInList = false): CustomerField {
		$field = new CustomerField();
		$field->setCollectionId($collectionId);
		$field->setType($type);
		$field->setName($name);
		$field->setSettings($settings);
		$field->setIncludeInList((int)$includeInList);
		return $this->mapper->insert($field);
	}

	public function update(int $id, int $collectionId, ?string $newType, ?string $newName, ?string $newSettings, ?bool $newIncludeInList): CustomerField {
		try {
			$field = $this->mapper->find($id, $collectionId);
			
			if (!is_null($newType)) {
				$field->setType($newType);
			}
			if (!is_null($newName) && strlen($newName) >= 3) {
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

	public function delete($id, $collectionId): CustomerField {
		try {
			$field = $this->mapper->find($id, $collectionId);
			$this->mapper->delete($field);
			return $field;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
