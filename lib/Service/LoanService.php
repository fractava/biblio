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

	public function update(int $collectionId, int $id, ?int $until, ?int $historySubEntryOf = null) {
		try {
			return $this->atomic(function () use ($collectionId, $id, $until, $historySubEntryOf) {
				$loan = $this->mapper->find($collectionId, $id);

				return $this->updateLoan($collectionId, $loan, $until, $historySubEntryOf);
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function updateByItemInstanceBarcode(int $collectionId, string $barcode, ?int $until, ?int $historySubEntryOf = null) {
		try {
			return $this->atomic(function () use ($collectionId, $barcode, $until, $historySubEntryOf) {
				$loan = $this->mapper->findByItemInstanceBarcode($collectionId, $barcode);

				return $this->updateLoan($collectionId, $loan, $until, $historySubEntryOf);
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	private function updateLoan(int $collectionId, Loan $loan, ?int $until, ?int $historySubEntryOf = null) {
		$unmodifiedLoan = $loan->jsonSerialize();
		
		if (!is_null($until)) {
			$loan->setUntil($until);
		} else {
			return $loan;
		}

		$loan = $this->mapper->update($loan);

		$historyEntry = $this->historyEntryService->create(
			type: "loan.update",
			collectionId: $collectionId,
			subEntryOf: $historySubEntryOf,
			properties: json_encode([ "before" => $unmodifiedLoan, "after" => $loan ]),
			itemInstanceId: $loan->getItemInstanceId(),
			customerId: $loan->getCustomerId(),
			loanId: $loan->getId(),
		);

		return $loan;
	}

	public function delete(int $collectionId, int $id, ?int $historySubEntryOf = null) {
		try {
			return $this->atomic(function () use ($collectionId, $id, $historySubEntryOf) {
				$loan = $this->mapper->find($collectionId, $id);
				return $this->deleteLoan($collectionId, $loan, $historySubEntryOf);
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function deleteByItemInstanceBarcode(int $collectionId, string $barcode, ?int $historySubEntryOf = null) {
		try {
			return $this->atomic(function () use ($collectionId, $barcode, $historySubEntryOf) {
				$loan = $this->mapper->findByItemInstanceBarcode($collectionId, $barcode);
				return $this->deleteLoan($collectionId, $loan, $historySubEntryOf);
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	private function deleteLoan(int $collectionId, Loan $loan, ?int $historySubEntryOf = null) {
		$this->mapper->delete($loan);

		$historyEntry = $this->historyEntryService->create(
			type: "loan.delete",
			collectionId: $collectionId,
			subEntryOf: $historySubEntryOf,
			properties: json_encode([ "before" => $loan, "after" => new \ArrayObject() ]),
			itemInstanceId: $loan->getItemInstanceId(),
			customerId: $loan->getCustomerId(),
			loanId: $loan->getId(),
		);

		return $loan;
	}
}
