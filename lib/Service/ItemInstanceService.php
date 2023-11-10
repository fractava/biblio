<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\TTransactional;
use OCP\IDBConnection;

use OCA\Biblio\Errors\ItemInstanceNotFound;

use OCA\Biblio\Db\ItemInstance;
use OCA\Biblio\Db\ItemInstanceMapper;

use OCA\Biblio\Traits\ApiObjectService;

class ItemInstanceService {
	use ApiObjectService;
	use TTransactional;

	public const ITEM_INCLUDE = "item";
	public const LOAN_INCLUDE = "loan";
	public const LOAN_CUSTOMER_INCLUDE = "loan_customer";
	public const FIELDS_INCLUDE = "fields";

	/** @var ItemInstanceMapper */
	private $mapper;

	/** @var LoanFieldValueService */
	private $fieldValueService;

	/** @var ItemService */
	private $itemService;

	public function __construct(
		ItemInstanceMapper $mapper,
		LoanFieldValueService $fieldValueService,
		HistoryEntryService $historyEntryService,
		ItemService $itemService,
		IDBConnection $db,
	) {
		$this->mapper = $mapper;
		$this->fieldValueService = $fieldValueService;
		$this->historyEntryService = $historyEntryService;
		$this->itemService = $itemService;
		$this->db = $db;
	}

	public function getApiObjectFromEntities(int $collectionId, $entities, bool $includeModel, bool $includeItem, bool $includeLoan, bool $includeLoanCustomer, bool $includeFields, ?array $fieldFilters = null) {
		$result = [];

		if ($includeModel) {
			$result = $entities["instance"]->jsonSerialize();
		}

		if ($includeItem) {
			$result["item"] = $entities["item"]->jsonSerialize();
		}

		if ($includeLoan) {
			$result["loan"] = $entities["loan"]->jsonSerialize();

			if ($includeLoanCustomer) {
				$result["loan"]["customer"] = $entities["loanCustomer"]->jsonSerialize();
			}

			if ($includeFields) {
				$loanId = $entities["loan"]->getId();
				if (isset($loanId)) {
					$result = array_merge($result, [
						"fieldValues" => $this->fieldValueService->findAll($collectionId, $loanId, ["model", "field"], $fieldFilters),
					]);
				}
			}
		}

		return $result;
	}

	public function findAll(int $collectionId, array $includes, ?array $filters, ?string $sort = null, bool $sortReverse = null, ?int $limit = null, ?int $offset = null): array {
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeItem = $this->shouldInclude(self::ITEM_INCLUDE, $includes);
		$includeLoan = $this->shouldInclude(self::LOAN_INCLUDE, $includes);
		$includeLoanCustomer = $includeLoan && $this->shouldInclude(self::LOAN_CUSTOMER_INCLUDE, $includes);
		$includeFields = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

		[$entities, $meta] = $this->mapper->findAll($collectionId, $filters, $sort, $sortReverse, $limit, $offset);

		$fieldFilters = $this->getFieldFiltersOutOfFilters($filters);

		$results = [];

		foreach ($entities as $entity) {
			$results[] = $this->getApiObjectFromEntities($collectionId, $entity, $includeModel, $includeItem, $includeLoan, $includeLoanCustomer, $includeFields, $fieldFilters);
		}

		return [$results, $meta];
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemInstanceNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $collectionId, int $id) {
		try {
			return $this->mapper->find($collectionId, $id);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function findByBarcode(int $collectionId, string $barcode) {
		try {
			return $this->mapper->findByBarcode($collectionId, $barcode);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $barcode, int $itemId, ?int $historySubEntryOf = null) {
		return $this->atomic(function () use ($collectionId, $barcode, $itemId, $historySubEntryOf) {
			$item = $this->itemService->find($collectionId, $itemId, ["model"]);

			$itemInstance = new ItemInstance();
			$itemInstance->setBarcode($barcode);
			$itemInstance->setItemId($item["id"]);

			$itemInstance = $this->mapper->insert($itemInstance);

			$historyEntry = $this->historyEntryService->create(
				type: "itemInstance.create",
				collectionId: $collectionId,
				subEntryOf: $historySubEntryOf,
				properties: json_encode([ "before" => new \ArrayObject(), "after" => $itemInstance ]),
				itemId: $item["id"],
				itemInstanceId: $itemInstance->getId(),
			);

			return $itemInstance;
		}, $this->db);
	}

	public function delete(int $collectionId, int $id, ?int $historySubEntryOf = null) {
		try {
			return $this->atomic(function () use ($collectionId, $id, $historySubEntryOf) {
				$itemInstance = $this->mapper->find($collectionId, $id);

				$this->mapper->delete($itemInstance);

				$historyEntry = $this->historyEntryService->create(
					type: "itemInstance.delete",
					collectionId: $collectionId,
					subEntryOf: $historySubEntryOf,
					properties: json_encode([ "before" => $itemInstance, "after" => new \ArrayObject() ]),
					itemId: $itemInstance->getItemId(),
					itemInstanceId: $itemInstance->getId(),
				);

				return $itemInstance;
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function deleteByBarcode(int $collectionId, string $barcode, ?int $historySubEntryOf = null) {
		try {
			return $this->atomic(function () use ($collectionId, $barcode, $historySubEntryOf) {
				$itemInstance = $this->mapper->findByBarcode($collectionId, $barcode);

				$this->mapper->delete($itemInstance);

				$historyEntry = $this->historyEntryService->create(
					type: "itemInstance.delete",
					collectionId: $collectionId,
					subEntryOf: $historySubEntryOf,
					properties: json_encode([ "before" => $itemInstance, "after" => new \ArrayObject() ]),
					itemId: $itemInstance->getItemId(),
					itemInstanceId: $itemInstance->getId(),
				);

				return $itemInstance;
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function getBarcodePrefix(int $collectionId, int $itemId) {
		return $this->mapper->getBarcodePrefix($collectionId, $itemId);
	}
}
