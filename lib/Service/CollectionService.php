<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\CollectionNotFound;

use OCA\Biblio\Db\Collection;
use OCA\Biblio\Db\CollectionMapper;

class CollectionService {
	/** @var CollectionMapper */
	private $mapper;

	/** @var CollectionMemberService */
	private $memberService;

	public function __construct(CollectionMapper $mapper, CollectionMemberService $memberService) {
		$this->mapper = $mapper;
		$this->memberService = $memberService;
	}

	public function findAll(string $userId): array {
		return $this->mapper->findAll($userId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new CollectionNotFound($e->getMessage());
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

	public function create(string $name, string $itemFieldsOrder, string $customerFieldsOrder, string $firstMember) {
		$collection = new Collection();
		$collection->setName($name);
		$collection->setItemFieldsOrder($itemFieldsOrder);
		$collection->setCustomerFieldsOrder($customerFieldsOrder);

		$collection = $this->mapper->insert($collection);

		$collectionId = $collection->getId();

		$this->memberService->create($collectionId, $firstMember);

		return $collection;
	}

	public function update(int $id, ?string $name, ?string $itemFieldsOrder, ?string $customerFieldsOrder) {
		try {
			$collection = $this->mapper->find($id);
			
			if (!is_null($name)) {
				$collection->setName($name);
			}

			if (!is_null($itemFieldsOrder)) {
				$collection->setItemFieldsOrder($itemFieldsOrder);
			}

			if (!is_null($customerFieldsOrder)) {
				$collection->setCustomerFieldsOrder($customerFieldsOrder);
			}

			return $this->mapper->update($collection);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $id) {
		try {
			$collection = $this->mapper->find($id);
			$this->mapper->delete($collection);
			return $collection;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
