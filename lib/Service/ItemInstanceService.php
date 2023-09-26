<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\ItemInstance;
use OCA\Biblio\Db\ItemInstanceMapper;

use OCA\Biblio\Service\ItemFieldValueService;

class ItemInstanceService {

	/** @var ItemInstanceMapper */
	private $mapper;

	public function __construct(ItemInstanceMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $itemId): array {
		return $this->mapper->findAll($itemId);
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

	public function create(string $barcode, int $itemId, int $loanedTo, int $loanedUntil) {
		$itemInstance = new ItemInstance();
		$itemInstance->setBarcode($barcode);
		$itemInstance->setItemId($itemId);
        $itemInstance->setLoanedTo($loanedTo);
        $itemInstance->setLoanedUntil($loanedUntil);

		$itemInstance = $this->mapper->insert($itemInstance);

		return $itemInstance;
	}

	public function update(int $id, string $barcode, int $loanedTo, int $loanedUntil) {
		try {
			$itemInstance = $this->mapper->find($id);
			
			if (!is_null($barcode)) {
				$itemInstance->setBarcode($barcode);
			}
            if (!is_null($loanedTo)) {
				$itemInstance->setLoanedTo($loanedTo);
			}
            if (!is_null($loanedUntil)) {
				$itemInstance->setLoanedUntil($loanedUntil);
			}

			return $this->mapper->update($itemInstance);
		} catch (Exception $e) {
			$this->handleException($e);
		}
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
}
