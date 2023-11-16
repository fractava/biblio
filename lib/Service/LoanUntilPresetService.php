<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\LoanUntilPresetNotFound;

use OCA\Biblio\Db\LoanUntilPreset;
use OCA\Biblio\Db\LoanUntilPresetMapper;

class LoanUntilPresetService {
	/** @var LoanUntilPresetMapper */
	private $mapper;

	public function __construct(LoanUntilPresetMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $collectionId): array {
		return $this->mapper->findAll($collectionId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new LoanUntilPresetNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $collectionId, int $id): LoanUntilPreset {
		try {
			return $this->mapper->find($collectionId, $id);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $type, string $name, int $timestamp): LoanUntilPreset {
		$preset = new LoanUntilPreset();
		$preset->setCollectionId($collectionId);
		$preset->setType($type);
		$preset->setName($name);
		$preset->setTimestamp($timestamp);

		return $this->mapper->insert($preset);
	}

	public function update(int $collectionId, int $id, ?string $type, ?string $name, ?int $timestamp): LoanUntilPreset {
		try {
			$preset = $this->mapper->find($collectionId, $id);

			if (!is_null($type) && ($type === "relative" || $type === "absolute")) {
				$preset->setType($type);
			}

			if (!is_null($name)) {
				$preset->setName($name);
			}

			if (!is_null($timestamp)) {
				$preset->setTimestamp($timestamp);
			}

			return $this->mapper->update($preset);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $collectionId, int $id): LoanUntilPreset {
		try {
			$preset = $this->mapper->find($collectionId, $id);
			$this->mapper->delete($preset);
			return $preset;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
