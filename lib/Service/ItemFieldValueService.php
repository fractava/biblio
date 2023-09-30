<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\ItemFieldValue;
use OCA\Biblio\Db\ItemFieldValueMapper;

use OCA\Biblio\Traits\ApiObjectService;

class ItemFieldValueService {
	use ApiObjectService;

	const FIELD_INCLUDE = 'field';

	/** @var ItemFieldValueMapper */
	private $mapper;

	public function __construct(ItemFieldValueMapper $mapper) {
		$this->mapper = $mapper;
	}

	private function copyValues($entity, bool $includeModel, bool $includeField) {
        $result = [];

        if($includeModel) {
            $result["itemId"] = $entity->getItemId();
            $result["fieldId"] = $entity->getFieldId();
            $result["value"] = $entity->getValue();
        }

        if($includeField) {
            $result["collectionId"] = $entity->getCollectionId();
            $result["name"] = $entity->getName();
            $result["type"] = $entity->getType();
            $result["settings"] = $entity->getSettings();
            $result["includeInList"] = $entity->getIncludeInList();
        }

        return $result;
    }

	public function findAll(int $collectionId, int $itemId, array $includes, ?string $filter, ?int $limit, ?int $offset): array {
		$filters = $this->parseFilterString($filter);
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeField = $this->shouldInclude(self::FIELD_INCLUDE, $includes);

		if($includeField) {
			$query = $this->mapper->findAllIncludingFields($collectionId, $itemId, $filters);
		} else {
			$query = $this->mapper->findAll($itemId, $filters);
		}

		$result = [];

        foreach ($query as $itemFieldValue) {
            $result[] = $this->copyValues($itemFieldValue, $includeModel, $includeField);
        }

		return $result;
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(array $parameters, array $includes): ItemFieldValue {
		try {
			$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
			$includeField = $this->shouldInclude(self::FIELD_INCLUDE, $includes);
			
			if($includeField) {
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
