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

	public function findAll(int $collectionId, array $includes, ?string $filter, ?int $limit, ?int $offset): array {
		if(!isset($include)) {
			$include = self::MODEL_INCLUDE;
		}

		$includes = $this->parseIncludesString($include);
		$filters = $this->parseFilterString($filter);
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		$includeFields = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

		$query = $this->mapper->findAll($collectionId, $filters, $limit, $offset);

		$results = [];

		if(isset($filters["fieldValues_includeInList"])) {
			$fieldFilters = ["includeInList" => $filters["fieldValues_includeInList"]];
		} else {
			$fieldFilters = [];
		}

		foreach ($query as $item) {
			$result = [];

			if($includeModel) {
				$result = $item->jsonSerialize();
			}

			if($includeFields) {
				$fieldValues = $this->fieldValueService->findAll($collectionId, $item->getId(), "model+field", $fieldFilters);

				$result = array_merge($result, [
					"fieldValues" => $fieldValues,
				]);
			}

			$results[] = $result;
		}

		return $results;
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id, int $collectionId) {
		try {
			return $this->mapper->find($id, $collectionId);
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

	public function update(int $id, int $collectionId, string $newTitle) {
		try {
			$item = $this->mapper->find($id, $collectionId);
			
			if (!is_null($title)) {
				$item->setTitle($title);
			}

			return $this->mapper->update($item);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $id, int $collectionId) {
		try {
			$item = $this->mapper->find($id, $collectionId);
			$this->mapper->delete($item);
			return $item;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
