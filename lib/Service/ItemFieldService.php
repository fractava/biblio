<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\TTransactional;
use OCP\IDBConnection;

use OCA\Biblio\Errors\ItemFieldNotFound;

use OCA\Biblio\Db\ItemField;
use OCA\Biblio\Db\ItemFieldMapper;

class ItemFieldService {
	use TTransactional;

	/** @var ItemFieldMapper */
	private $mapper;

	/** @var HistoryEntryService */
	private $historyEntryService;

	/** @var IDBConnection */
	private $db;

	public function __construct(
		ItemFieldMapper $mapper,
		HistoryEntryService $historyEntryService,
		IDBConnection $db,
	) {
		$this->mapper = $mapper;
		$this->historyEntryService = $historyEntryService;
		$this->db = $db;
	}

	public function findAll(int $collectionId): array {
		return $this->mapper->findAll($collectionId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new ItemFieldNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id, int $collectionId): ItemField {
		try {
			return $this->mapper->find($id, $collectionId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(
		int $collectionId,
		string $type,
		string $name,
		string $settings,
		bool $includeInList = false,
		?int $historySubEntryOf = null,
	): ItemField {
		return $this->atomic(function () use ($collectionId, $type, $name, $settings, $includeInList, $historySubEntryOf) {
			$field = new ItemField();
			$field->setCollectionId($collectionId);
			$field->setType($type);
			$field->setName($name);
			$field->setSettings($settings);
			$field->setIncludeInList((int)$includeInList);

			$field = $this->mapper->insert($field);

			$this->historyEntryService->create(
				type: "itemField.create",
				collectionId: $collectionId,
				subEntryOf: $historySubEntryOf,
				properties: json_encode(["before" => new \ArrayObject(), "after" => $field]),
				itemFieldId: $field->getId(),
			);

			return $field;
		}, $this->db);
	}

	public function update(
		int $id,
		int $collectionId,
		?string $newType,
		?string $newName,
		?string $newSettings,
		?bool $newIncludeInList,
		?int $historySubEntryOf = null,
	): ItemField {
		try {
			return $this->atomic(function () use ($id, $collectionId, $newType, $newName, $newSettings, $newIncludeInList, $historySubEntryOf) {
				$field = $this->mapper->find($id, $collectionId);
				$unmodifiedField = $field->jsonSerialize();
			
				if (!is_null($newType)) {
					$field->setType($newType);
				}
				if (!is_null($newName) && strlen($newName) >= 3) {
					$field->setName($newName);
				}
				if (!is_null($newSettings)) {
					$field->setSettings($newSettings);
				}
				if (!is_null($newIncludeInList)) {
					$field->setIncludeInList((int)$newIncludeInList);
				}

				$field = $this->mapper->update($field);

				$this->historyEntryService->create(
					type: "itemField.update",
					collectionId: $collectionId,
					subEntryOf: $historySubEntryOf,
					properties: json_encode(["before" => $unmodifiedField, "after" => $field]),
					itemFieldId: $field->getId(),
				);

				return $field;
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete($id, $collectionId, ?int $historySubEntryOf = null): ItemField {
		try {
			return $this->atomic(function () use ($id, $collectionId) {
				$field = $this->mapper->find($id, $collectionId);

				$historyEntry = $this->historyEntryService->create(
					type: "itemField.delete",
					collectionId: $field->getCollectionId(),
					subEntryOf: $historySubEntryOf,
					properties: json_encode(["before" => $field, "after" => new \ArrayObject()]),
					itemFieldId: $field->getId(),
				);

				$this->mapper->delete($field);

				return $field;
			}, $this->db);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
