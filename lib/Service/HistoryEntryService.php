<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\HistoryEntryNotFound;

use OCA\Biblio\Db\HistoryEntry;
use OCA\Biblio\Db\HistoryEntryMapper;
use OCA\Biblio\Traits\ApiObjectService;

class HistoryEntryService {
	use ApiObjectService;

	public const SUB_ENTRIES_INCLUDE = 'subEntries';
	public const ITEM_INCLUDE = 'item';

	/** @var string */
	private $userId;

	/** @var HistoryEntryMapper */
	private $mapper;

	/** @var ItemService */
	private $itemService;

	public function __construct(
		$userId,
		HistoryEntryMapper $mapper,
		ItemService $itemService,
	) {
		$this->userId = $userId;
		$this->mapper = $mapper;
		$this->itemService = $itemService;
	}

	public function getApiObjectFromEntity(
		int $collectionId,
		$entity,
		bool $includeModel,
		bool $includeSubEntries,
		bool $includeItem,
		?string $sort
	) {
		$result = [];

		if ($includeModel) {
			$result = $entity->jsonSerialize();
		}

		if ($includeSubEntries) {
			[$subEntries, $meta] = $this->mapper->findAll($collectionId, $entity->getId(), [], $sort, false, 0, 0);
			$result["subEntries"] = $subEntries;
		}

		/*if ($includeItem) {
			try {
				$result["item"] = $this->itemService->find($collectionId, $entity->getItemId(), ["model"]);
			} catch (Exception $e) {
				$result["item"] = new \ArrayObject();
			}
		}*/

		return $result;
	}

	public function findAll(int $collectionId, array $includes, ?array $filters, ?string $sort = null, bool $sortReverse = null, ?int $limit = null, ?int $offset = null): array {
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeSubEntries = $this->shouldInclude(self::SUB_ENTRIES_INCLUDE, $includes);
		$includeItem = $this->shouldInclude(self::ITEM_INCLUDE, $includes);

		[$entities, $meta] = $this->mapper->findAll($collectionId, null, $filters, $sort, $sortReverse, $limit, $offset);

		$results = [];

		foreach ($entities as $entry) {
			$results[] = $this->getApiObjectFromEntity(
				collectionId: $collectionId,
				entity: $entry,
				includeModel: $includeModel,
				includeSubEntries: $includeSubEntries,
				includeItem: $includeItem,
				sort: $sort,
			);
		}

		return [$results, $meta];
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new HistoryEntryNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $collectionId, int $id, array $includes) {
		try {
			$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
			$includeSubEntries = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

			$entry = $this->mapper->find($collectionId, $id);

			return $this->getApiObjectFromEntity($collectionId, $entry, $includeModel, $includeSubEntries, "timestamp");
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(
		string $type,
		int $collectionId,
		string $properties,
		?int $subEntryOf = null,
		?int $collectionMemberId = null,
		?int $itemId = null,
		?int $itemFieldId = null,
		?int $itemFieldValueId = null,
		?int $itemInstanceId = null,
		?int $loanId = null,
		?int $loanFieldId = null,
		?int $loanFieldValueId = null,
		?int $customerId = null,
		?int $customerFieldId = null,
		?int $customerFieldValueId = null,
	) {
		$entry = new HistoryEntry();
		
		$entry->setType($type);
		$entry->setTimestamp(time());
		$entry->setUserId($this->userId);
		$entry->setProperties($properties);

		if (isset($subEntryOf)) {
			$entry->setSubEntryOf($subEntryOf);
		}

		if (isset($collectionId)) {
			$entry->setCollectionId($collectionId);
		}

		if (isset($collectionMemberId)) {
			$entry->setCollectionMemberId($collectionMemberId);
		}

		if (isset($itemId)) {
			$entry->setItemId($itemId);
		}

		if (isset($itemFieldId)) {
			$entry->setItemFieldId($itemFieldId);
		}

		if (isset($itemFieldValueId)) {
			$entry->setItemFieldValueId($itemFieldValueId);
		}

		if (isset($itemInstanceId)) {
			$entry->setItemInstanceId($itemInstanceId);
		}

		if (isset($loanId)) {
			$entry->setLoanId($loanId);
		}

		if (isset($loanFieldId)) {
			$entry->setLoanFieldId($loanFieldId);
		}

		if (isset($loanFieldValueId)) {
			$entry->setLoanFieldValueId($loanFieldValueId);
		}

		if (isset($customerId)) {
			$entry->setCustomerId($customerId);
		}

		if (isset($customerFieldId)) {
			$entry->setCustomerFieldId($customerFieldId);
		}

		if (isset($customerFieldValueId)) {
			$entry->setCustomerFieldValueId($customerFieldValueId);
		}

		$entry = $this->mapper->insert($entry);

		return $entry;
	}

	public function delete(int $collectionId, int $id) {
		try {
			$entry = $this->mapper->find($collectionId, $id);
			$this->mapper->delete($entry);
			return $entry;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
