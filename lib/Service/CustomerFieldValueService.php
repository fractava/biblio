<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\CustomerFieldValueNotFound;

use OCA\Biblio\Db\CustomerFieldValue;
use OCA\Biblio\Db\CustomerFieldValueMapper;

use OCA\Biblio\Traits\ApiObjectService;

class CustomerFieldValueService {
	use ApiObjectService;

	public const FIELD_INCLUDE = 'field';

	/** @var CustomerFieldValueMapper */
	private $mapper;

	public function __construct(CustomerFieldValueMapper $mapper) {
		$this->mapper = $mapper;
	}

	private function copyValues($entity, bool $includeModel, bool $includeField) {
		$result = [];

		if ($includeModel) {
			$result["customerId"] = $entity->getCustomerId();
			$result["fieldId"] = $entity->getFieldId();
			$result["value"] = $entity->getValue();
		}

		if ($includeField) {
			$result["collectionId"] = $entity->getCollectionId();
			$result["name"] = $entity->getName();
			$result["type"] = $entity->getType();
			$result["settings"] = $entity->getSettings();
			$result["includeInList"] = $entity->getIncludeInList();
		}

		return $result;
	}

	public function findAll(int $collectionId, int $customerId, array $includes, ?array $filters, ?int $limit = null, ?int $offset = null): array {
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeField = $this->shouldInclude(self::FIELD_INCLUDE, $includes);

		if ($includeField) {
			$query = $this->mapper->findAllIncludingFields($collectionId, $customerId, $filters);
		} else {
			$query = $this->mapper->findAll($customerId, $filters);
		}

		$result = [];

		foreach ($query as $fieldValue) {
			$result[] = $this->copyValues($fieldValue, $includeModel, $includeField);
		}

		return $result;
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new CustomerFieldValueNotFound($e->getMessage());
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

	public function create(int $customerId, int $fieldId, string $value): CustomerFieldValue {
		$fieldValue = new CustomerFieldValue();
		$fieldValue->setCustomerId($customerId);
		$fieldValue->setFieldId($fieldId);
		$fieldValue->setValue($value);
		return $this->mapper->insert($fieldValue);
	}

	public function update(int $id, string $newValue): CustomerFieldValue {
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

	public function updateByCustomerAndFieldId(int $customerId, int $fieldId, string $newValue): CustomerFieldValue {
		if (!is_null($newValue)) {
			try {
				$fieldValue = $this->mapper->find([
					"customerId" => $customerId,
					"fieldId" => $fieldId,
				]);
				$fieldValue->setValue($newValue);
				return $this->mapper->update($fieldValue);
			} catch (DoesNotExistException $e) {
				return $this->create($customerId, $fieldId, $newValue);
			} catch (\Exception $e) {
				$this->handleException($e);
			}
		}
	}

	public function delete($id): CustomerFieldValue {
		try {
			$fieldValue = $this->mapper->find($id);
			$this->mapper->delete($fieldValue);
			return $fieldValue;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function deleteByCustomerAndFieldId(int $customerId, int $fieldId): CustomerFieldValue {
		try {
			$fieldValue = $this->mapper->find([
				"customerId" => $customerId,
				"fieldId" => $fieldId,
			]);
			$this->mapper->delete($fieldValue);
			return $fieldValue;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
