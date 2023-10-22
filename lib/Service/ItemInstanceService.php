<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\ItemInstanceNotFound;

use OCA\Biblio\Db\ItemInstance;
use OCA\Biblio\Db\ItemInstanceMapper;

use OCA\Biblio\Traits\ApiObjectService;

class ItemInstanceService {
	use ApiObjectService;

	const ITEM_INCLUDE = "item";
	const LOAN_INCLUDE = "loan";
	const LOAN_CUSTOMER_INCLUDE = "loan_customer";
	const FIELDS_INCLUDE = "fields";

	/** @var ItemInstanceMapper */
	private $mapper;

	public function __construct(ItemInstanceMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function getApiObjectFromEntities(int $collectionId, $entities, bool $includeModel, bool $includeItem, bool $includeLoan, bool $includeLoanCustomer, bool $includeFields, ?array $fieldFilters = null) {
		$result = [];

		if($includeModel) {
			$result = $entities["instance"]->jsonSerialize();
		}

		if($includeItem) {
			$result["item"] = $entities["item"]->jsonSerialize();
		}

		if($includeLoan) {
			$result["loan"] = $entities["loan"]->jsonSerialize();

			if($includeLoanCustomer) {
				$result["loan"]["customer"] = $entities["loanCustomer"]->jsonSerialize();
			}
		}

		if($includeFields) {
			$result = array_merge($result, [
				"fieldValues" => $this->fieldValueService->findAll($collectionId, $entity->getId(), ["model", "field"], $fieldFilters),
			]);
		}

		return $result;
	}

	public function findAll(int $collectionId, array $includes, ?array $filters, ?string $sort = null, bool $sortReverse = null, ?int $limit = null, ?int $offset = null): array {
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeItem = $this->shouldInclude(self::ITEM_INCLUDE, $includes);
		$includeLoan = $this->shouldInclude(self::LOAN_INCLUDE, $includes);
		$includeLoanCustomer = $includeLoan && $this->shouldInclude(self::LOAN_CUSTOMER_INCLUDE, $includes);
		$includeFields = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

		list($entities, $meta) = $this->mapper->findAll($collectionId, $filters, $sort, $sortReverse, $limit, $offset);

		//$fieldFilters = $this->getFieldFiltersOutOfFilters($filters);

		$results = [];

		foreach ($entities as $entity) {
			$results[] = $this->getApiObjectFromEntities($collectionId, $entity, $includeModel, $includeItem, $includeLoan, $includeLoanCustomer, $includeFields, $fieldFilters);
		}

		return array($results, $meta);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemInstanceNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id) {
		try {
			return $this->mapper->find($id);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

    public function findByBarcode(string $barcode) {
		try {
			return $this->mapper->findByBarcode($barcode);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(string $barcode, int $itemId) {
		$itemInstance = new ItemInstance();
		$itemInstance->setBarcode($barcode);
		$itemInstance->setItemId($itemId);

		$itemInstance = $this->mapper->insert($itemInstance);

		return $itemInstance;
	}

	public function delete(int $id) {
		try {
			$itemInstance = $this->mapper->find($id);
			$this->mapper->delete($itemInstance);
			return $itemInstance;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function deleteByBarcode(string $barcode) {
		try {
			$itemInstance = $this->mapper->findByBarcode($barcode);
			$this->mapper->delete($itemInstance);
			return $itemInstance;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
