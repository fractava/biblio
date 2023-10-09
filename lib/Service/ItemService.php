<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemMapper;
use OCA\Biblio\Service\ItemFieldValueService;
use OCA\Biblio\Traits\ApiObjectService;

class ItemService {
	use ApiObjectService;

	const FIELDS_INCLUDE = 'fields';

	/** @var ItemMapper */
	private $mapper;

	/** @var ItemFieldValueService */
	private $fieldValueService;

	public function __construct(ItemMapper $mapper, ItemFieldValueService $fieldValueService) {
		$this->mapper = $mapper;
		$this->fieldValueService = $fieldValueService;
	}

	private function getFieldFiltersOutOfItemFilters(?array $filters) {
		if(isset($filters["fieldValues_includeInList"])) {
			return ["includeInList" => $filters["fieldValues_includeInList"]];
		} else {
			return [];
		}
	}

	public function getApiObjectFromEntity(int $collectionId, $entity, bool $includeModel, bool $includeFields, ?array $fieldFilters = null) {
		$result = [];

		if($includeModel) {
			$result = $entity->jsonSerialize();
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
		$includeFields = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

		list($entities, $meta) = $this->mapper->findAll($collectionId, $filters, $sort, $sortReverse, $limit, $offset);

		$fieldFilters = $this->getFieldFiltersOutOfItemFilters($filters);

		$results = [];

		foreach ($entities as $item) {
			$results[] = $this->getApiObjectFromEntity($collectionId, $item, $includeModel, $includeFields, $fieldFilters);
		}

		return array($results, $meta);
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

			$fieldFilters = $this->getFieldFiltersOutOfItemFilters($filters);

			return $this->getApiObjectFromEntity($collectionId, $item, $includeModel, $includeFields, $fieldFilters);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $title) {
		$item = new Item();
		$item->setCollectionId($collectionId);
		$item->setTitle($title);

		$item = $this->mapper->insert($item);

		return $item;
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
