<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\LoanFieldNotFound;

use OCA\Biblio\Db\LoanField;
use OCA\Biblio\Db\LoanFieldMapper;

class LoanFieldService {
	/** @var LoanFieldMapper */
	private $mapper;

	public function __construct(LoanFieldMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $collectionId): array {
		return $this->mapper->findAll($collectionId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new LoanFieldNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id, int $collectionId): LoanField {
		try {
			return $this->mapper->find($id, $collectionId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $type, string $name, string $settings): LoanField {
		$field = new LoanField();
		$field->setCollectionId($collectionId);
		$field->setType($type);
		$field->setName($name);
		$field->setSettings($settings);
		return $this->mapper->insert($field);
	}

	public function update(int $id, int $collectionId, ?string $newType, ?string $newName, ?string $newSettings): LoanField {
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

			return $this->mapper->update($field);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete($id, $collectionId): LoanField {
		try {
			$field = $this->mapper->find($id, $collectionId);
			$this->mapper->delete($field);
			return $field;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
