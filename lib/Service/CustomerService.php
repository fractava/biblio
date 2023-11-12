<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\TTransactional;
use OCP\IDBConnection;

use OCA\Biblio\Errors\CustomerNotFound;

use OCA\Biblio\Db\Customer;
use OCA\Biblio\Db\CustomerMapper;
use OCA\Biblio\Traits\ApiObjectService;

class CustomerService {
	use ApiObjectService;
	use TTransactional;

	public const FIELDS_INCLUDE = 'fields';

	/** @var CustomerMapper */
	private $mapper;

	/** @var CustomerFieldValueService */
	private $fieldValueService;

	/** @var HistoryEntryService */
	private $historyEntryService;

	/** @var IDBConnection */
	private $db;

	public function __construct(
		CustomerMapper $mapper,
		CustomerFieldValueService $fieldValueService,
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

		foreach ($entities as $customer) {
			$results[] = $this->getApiObjectFromEntity($collectionId, $customer, $includeModel, $includeFields, $fieldFilters);
		}

		return [$results, $meta];
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new CustomerNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $collectionId, int $id, array $includes) {
		try {
			$includeModel = $this->shouldInclude(self::MODEL_INCLUDE, $includes);
			$includeFields = $this->shouldInclude(self::FIELDS_INCLUDE, $includes);

			$customer = $this->mapper->find($collectionId, $id);

			$fieldFilters = $this->getFieldFiltersOutOfFilters($filters);

			return $this->getApiObjectFromEntity($collectionId, $customer, $includeModel, $includeFields, $fieldFilters);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $name, ?int $historySubEntryOf = null) {
		return $this->atomic(function () use ($collectionId, $name, $historySubEntryOf) {
			$customer = new Customer();
			$customer->setCollectionId($collectionId);
			$customer->setName($name);

			$customer = $this->mapper->insert($customer);

			$this->historyEntryService->create(
				type: "customer.create",
				collectionId: $collectionId,
				subEntryOf: $historySubEntryOf,
				properties: json_encode(["before" => new \ArrayObject(), "after" => $customer]),
				customerId: $customer->getId(),
			);

			return $customer;
		}, $this->db);
	}

	public function update(int $collectionId, int $id, string $name, ?int $historySubEntryOf = null) {
		try {
			return $this->atomic(function () use ($collectionId, $id, $name, $historySubEntryOf) {
				$customer = $this->mapper->find($collectionId, $id);
				$unmodifiedCustomer = $customer->jsonSerialize();
			
				if (!is_null($name)) {
					$customer->setName($name);
				} else {
					return $customer;
				}

				$this->historyEntryService->create(
					type: "customer.update",
					collectionId: $collectionId,
					subEntryOf: $historySubEntryOf,
					properties: json_encode(["before" => $unmodifiedCustomer, "after" => $customer]),
					customerId: $customer->getId(),
				);

				return $this->mapper->update($customer);
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $collectionId, int $id) {
		try {
			$customer = $this->mapper->find($collectionId, $id);
			$this->mapper->delete($customer);
			return $customer;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
