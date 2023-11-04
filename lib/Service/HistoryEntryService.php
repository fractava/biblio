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

	/** @var HistoryEntryMapper */
	private $mapper;

	public function __construct(HistoryEntryMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function getApiObjectFromEntity(int $collectionId, $entity, bool $includeModel, bool $includeSubEntries, ?string $sort) {
		$result = [];

		if ($includeModel) {
			$result = $entity->jsonSerialize();
		}

		if ($includeSubEntries) {
			$result["subEntries"] = $this->mapper->findAll($collectionId, $entity->getId(), [], $sort);
		}

		return $result;
	}

	public function findAll(int $collectionId, array $includes, ?array $filters, ?string $sort = null, bool $sortReverse = null, ?int $limit = null, ?int $offset = null): array {
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeSubEntries = $this->shouldInclude(self::SUB_ENTRIES_INCLUDE, $includes);

		[$entities, $meta] = $this->mapper->findAll($collectionId, null, $filters, $sort, $sortReverse, $limit, $offset);

		$results = [];

		foreach ($entities as $entry) {
			$results[] = $this->getApiObjectFromEntity($collectionId, $entry, $includeModel, $includeSubEntries, $sort);
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
		int $timestamp,
		string $properties,
		?int $subEntryOf = null,
		?int $collectionId = null,
		?int $collectionMemberId = null,
		?int $itemId = null,
		?int $itemFieldId = null,
		?int $itemFieldValueId = null,
		?int $loanId = null,
		?int $loanFieldId = null,
		?int $loanFieldValueId = null,
		?int $customerId = null,
		?int $customerFieldId = null,
		?int $customerFieldValueId = null,
	) {
		$entry = new HistoryEntry();
		
		$entry->setType($type);
		$entry->setTimestamp($timestamp);
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
