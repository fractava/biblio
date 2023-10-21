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

	/** @var ItemInstanceMapper */
	private $mapper;

	public function __construct(ItemInstanceMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $collectionId, array $includes, ?array $filters, ?string $sort = null, bool $sortReverse = null, ?int $limit = null, ?int $offset = null): array {
		$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
		//$includeFields = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

		list($entities, $meta) = $this->mapper->findAll($collectionId, $filters, $sort, $sortReverse, $limit, $offset);

		//$fieldFilters = $this->getFieldFiltersOutOfFilters($filters);

		/*$results = [];

		foreach ($entities as $item) {
			$results[] = $this->getApiObjectFromEntity($collectionId, $item, $includeModel, $includeFields, $fieldFilters);
		}*/

		return array($entities, $meta);
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
