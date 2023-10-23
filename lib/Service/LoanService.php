<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\LoanNotFound;

use OCA\Biblio\Db\Loan;
use OCA\Biblio\Db\LoanMapper;

class LoanService {
	/** @var LoanMapper */
	private $mapper;
	
	/** @var ItemInstanceService */
	private $itemInstanceService;

	public function __construct(LoanMapper $mapper, ItemInstanceService $itemInstanceService) {
		$this->mapper = $mapper;
		$this->itemInstanceService = $itemInstanceService;
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

	public function create(int $itemInstanceId, int $customerId, int $until) {
		$newLoan = new Loan();
		$newLoan->setItemInstanceId($itemInstanceId);
		$newLoan->setCustomerId($customerId);
		$newLoan->setUntil($until);

		$newLoan = $this->mapper->insert($newLoan);

		return $newLoan;
	}

	public function createByItemInstanceBarcode(string $barcode, int $customerId, int $until) {
		$itemInstanceId = $this->itemInstanceService->findByBarcode($barcode)->getId();

		return $this->create($itemInstanceId, $customerId, $until);
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
