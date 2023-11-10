<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\TTransactional;
use OCP\IDBConnection;

use OCA\Biblio\Errors\LoanNotFound;

use OCA\Biblio\Db\Loan;
use OCA\Biblio\Db\LoanMapper;

class LoanService {
	use TTransactional;

	/** @var LoanMapper */
	private $mapper;
	
	/** @var ItemInstanceService */
	private $itemInstanceService;

	/** @var CustomerService */
	private $customerService;

	/** @var HistoryEntryService */
	private $historyEntryService;

	public function __construct(
		LoanMapper $mapper,
		ItemInstanceService $itemInstanceService,
		CustomerService $customerService,
		HistoryEntryService $historyEntryService,
		IDBConnection $db,
	) {
		$this->mapper = $mapper;
		$this->itemInstanceService = $itemInstanceService;
		$this->customerService = $customerService;
		$this->historyEntryService = $historyEntryService;
		$this->db = $db;
	}

	public function findAll(int $collectionId): array {
		return $this->mapper->findAll($collectionId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new LoanNotFound($e->getMessage());
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

	public function findByItemInstanceBarcode(string $barcode) {
		try {
			return $this->mapper->findByItemInstanceBarcode($barcode);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, int $itemInstanceId, int $customerId, int $until, ?int $historySubEntryOf = null) {
		return $this->atomic(function () use ($collectionId, $itemInstanceId, $customerId, $until, $historySubEntryOf) {
			$itemInstance = $this->itemInstanceService->find($collectionId, $itemInstanceId);
			$customer = $this->customerService->find($collectionId, $customerId, ["model"]);

			$loan = new Loan();
			$loan->setItemInstanceId($itemInstance->getId());
			$loan->setCustomerId($customer["id"]);
			$loan->setUntil($until);

			$loan = $this->mapper->insert($loan);

			$historyEntry = $this->historyEntryService->create(
				type: "loan.create",
				collectionId: $collectionId,
				subEntryOf: $historySubEntryOf,
				properties: json_encode([ "before" => new \ArrayObject(), "after" => $loan ]),
				itemInstanceId: $itemInstance->getId(),
				customerId: $customer["id"],
				loanId: $loan->getId(),
			);

			return $loan;
		}, $this->db);
	}

	public function createByItemInstanceBarcode(int $collectionId, string $barcode, int $customerId, int $until) {
		$itemInstanceId = $this->itemInstanceService->findByBarcode($collectionId, $barcode)->getId();

		return $this->create($collectionId, $itemInstanceId, $customerId, $until);
	}

	public function update(int $id, ?int $until) {
		try {
			$loan = $this->mapper->find($id);
			
			if (!is_null($until)) {
				$loan->setUntil($until);
			}

			return $this->mapper->update($loan);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function updateByItemInstanceBarcode(string $barcode, ?int $until) {
		try {
			$loan = $this->mapper->findByItemInstanceBarcode($barcode);

			if (!is_null($until)) {
				$loan->setUntil($until);
			}

			return $this->mapper->update($loan);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $id) {
		try {
			$loan = $this->mapper->find($id);
			$this->mapper->delete($loan);
			return $loan;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function deleteByItemInstanceBarcode(string $barcode) {
		try {
			$loan = $this->mapper->findByItemInstanceBarcode($barcode);
			$this->mapper->delete($loan);
			return $loan;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
