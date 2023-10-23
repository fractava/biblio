<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\LoanFieldValueNotFound;

use OCA\Biblio\Db\LoanFieldValue;
use OCA\Biblio\Db\LoanFieldValueMapper;

use OCA\Biblio\Traits\ApiObjectService;

class LoanFieldValueService {
	use ApiObjectService;

	public const FIELD_INCLUDE = 'field';

	/** @var LoanFieldValueMapper */
	private $mapper;

	public function __construct(LoanFieldValueMapper $mapper) {
		$this->mapper = $mapper;
	}

	private function copyValues($entity, bool $includeModel, bool $includeField) {
		$result = [];

		if ($includeModel) {
			$result["loanId"] = $entity->getLoanId();
			$result["fieldId"] = $entity->getFieldId();
			$result["value"] = $entity->getValue();
		}

		if ($includeField) {
			$result["collectionId"] = $entity->getCollectionId();
			$result["name"] = $entity->getName();
			$result["type"] = $entity->getType();
			$result["settings"] = $entity->getSettings();
		}

		return $result;
	}

	public function findAll(int $collectionId, int $loanId, array $includes, ?array $filters, ?int $limit = null, ?int $offset = null): array {
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeField = $this->shouldInclude(self::FIELD_INCLUDE, $includes);

		if ($includeField) {
			$query = $this->mapper->findAllIncludingFields($collectionId, $loanId, $filters);
		} else {
			$query = $this->mapper->findAll($loanId, $filters);
		}

		$result = [];

		foreach ($query as $loanFieldValue) {
			$result[] = $this->copyValues($loanFieldValue, $includeModel, $includeField);
		}

		return $result;
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new LoanFieldValueNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(array $parameters, array $includes): array {
		try {
			$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
			$includeField = $this->shouldInclude(self::FIELD_INCLUDE, $includes);
			
			if ($includeField) {
				$query = $this->mapper->findIncludingField($parameters);
			} else {
				$query = $this->mapper->find($parameters);
			}

			$result = $this->copyValues($query, $includeModel, $includeField);

			return $result;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $loanId, int $fieldId, string $value): LoanFieldValue {
		$fieldValue = new LoanFieldValue();
		$fieldValue->setLoanId($loanId);
		$fieldValue->setFieldId($fieldId);
		$fieldValue->setValue($value);
		return $this->mapper->insert($fieldValue);
	}

	public function update(int $id, string $newValue): LoanFieldValue {
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

	public function updateByLoanAndFieldId(int $loanId, int $fieldId, string $newValue): LoanFieldValue {
		if (!is_null($newValue)) {
			try {
				$fieldValue = $this->mapper->find([
					"loanId" => $loanId,
					"fieldId" => $fieldId,
				]);
				$fieldValue->setValue($newValue);
				return $this->mapper->update($fieldValue);
			} catch (DoesNotExistException $e) {
				return $this->create($loanId, $fieldId, $newValue);
			} catch (\Exception $e) {
				$this->handleException($e);
			}
		}
	}

	public function delete($id): LoanFieldValue {
		try {
			$fieldValue = $this->mapper->find($id);
			$this->mapper->delete($fieldValue);
			return $fieldValue;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function deleteByLoanAndFieldId(int $loanId, int $fieldId): LoanFieldValue {
		try {
			$fieldValue = $this->mapper->find([
				"loanId" => $loanId,
				"fieldId" => $fieldId,
			]);
			$this->mapper->delete($fieldValue);
			return $fieldValue;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
