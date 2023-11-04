<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\TTransactional;
use OCP\IDBConnection;

use OCA\Biblio\Errors\ItemNotFound;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemMapper;
use OCA\Biblio\Traits\ApiObjectService;

class ItemService {
	use ApiObjectService;
	use TTransactional;

	public const FIELDS_INCLUDE = 'fields';

	/** @var ItemMapper */
	private $mapper;

	/** @var ItemFieldValueService */
	private $fieldValueService;

	/** @var HistoryEntryService */
	private $historyEntryService;

	/** @var IDBConnection */
	private $db;

	public function __construct(
		ItemMapper $mapper,
		ItemFieldValueService $fieldValueService,
		HistoryEntryService $historyEntryService,
		IDBConnection $db,
	) {
		$this->mapper = $mapper;
		$this->fieldValueService = $fieldValueService;
		$this->historyEntryService = $historyEntryService;
		$this->db = $db;
	}

	public function getApiObjectFromEntity(int $collectionId, $entity, bool $includeModel, bool $includeFields, ?array $fieldFilters = null) {
		$result = [];

		if ($includeModel) {
			$result = $entity->jsonSerialize();
		}

		if ($includeFields) {
			$result = array_merge($result, [
				"fieldValues" => $this->fieldValueService->findAll($collectionId, $entity->getId(), ["model", "field"], $fieldFilters),
			]);
		}

		return $result;
	}

	public function findAll(int $collectionId, array $includes, ?array $filters, ?string $sort = null, bool $sortReverse = null, ?int $limit = null, ?int $offset = null): array {
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeFields = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

		[$entities, $meta] = $this->mapper->findAll($collectionId, $filters, $sort, $sortReverse, $limit, $offset);

		$fieldFilters = $this->getFieldFiltersOutOfFilters($filters);

		$results = [];

		foreach ($entities as $item) {
			$results[] = $this->getApiObjectFromEntity($collectionId, $item, $includeModel, $includeFields, $fieldFilters);
		}

		return [$results, $meta];
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $collectionId, int $id, array $includes) {
		try {
			$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
			$includeFields = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

			$item = $this->mapper->find($collectionId, $id);

			$fieldFilters = $this->getFieldFiltersOutOfFilters($filters);

			return $this->getApiObjectFromEntity($collectionId, $item, $includeModel, $includeFields, $fieldFilters);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $title) {
		return $this->atomic(function () use ($collectionId, $title) {
			$item = new Item();
			$item->setCollectionId($collectionId);
			$item->setTitle($title);

			$item = $this->mapper->insert($item);

			$historyEntry = $this->historyEntryService->create(
				type: "item.create",
				collectionId: $collectionId,
				properties: json_encode(["before" => [], "after" => $item->jsonSerialize()]),
				itemId: $item->getid(),
			);

			return $item;
		}, $this->db);
	}

	public function update(int $collectionId, int $id, string $title) {
		try {
			$item = $this->mapper->find($collectionId, $id);
			
			if (!is_null($title)) {
				$item->setTitle($title);
			}

			return $this->mapper->update($item);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $collectionId, int $id) {
		try {
			$item = $this->mapper->find($collectionId, $id);
			$this->mapper->delete($item);
			return $item;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
