<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Customer;
use OCA\Biblio\Db\CustomerMapper;
use OCA\Biblio\Service\CustomerFieldValueService;
use OCA\Biblio\Traits\ApiObjectService;

class CustomerService {
	use ApiObjectService;

	const FIELDS_INCLUDE = 'fields';

	/** @var CustomerMapper */
	private $mapper;

	/** @var CustomerFieldValueService */
	private $fieldValueService;

	public function __construct(CustomerMapper $mapper, CustomerFieldValueService $fieldValueService) {
		$this->mapper = $mapper;
		$this->fieldValueService = $fieldValueService;
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

		$fieldFilters = $this->getFieldFiltersOutOfFilters($filters);

		$results = [];

		foreach ($entities as $customer) {
			$results[] = $this->getApiObjectFromEntity($collectionId, $customer, $includeModel, $includeFields, $fieldFilters);
		}

		return array($results, $meta);
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

	public function create(int $collectionId, string $name) {
		$customer = new Customer();
		$customer->setCollectionId($collectionId);
		$customer->setName($name);

		$customer = $this->mapper->insert($customer);

		return $customer;
	}

	public function update(int $collectionId, int $id, string $name) {
		try {
			$customer = $this->mapper->find($collectionId, $id);
			
			if (!is_null($name)) {
				$customer->setName($name);
			}

			return $this->mapper->update($customer);
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
