<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Medium;
use OCA\Biblio\Db\MediumMapper;

use OCA\Biblio\Service\MediumFieldService;

class MediumService {

	/** @var MediumMapper */
	private $mapper;

	/** @var MediumFieldService */
	private $fieldService;

	public function __construct(MediumMapper $mapper, MediumFieldService $fieldService) {
		$this->mapper = $mapper;
		$this->fieldService = $fieldService;
	}

	public function findAll(int $libraryId): array {
		return $this->mapper->findAll($libraryId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new MediumNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id, int $libraryId) {
		try {
			return $this->mapper->find($id, $libraryId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(string $title, array $fields, int $libraryId) {
		$medium = new Medium();
		$medium->setTitle($title);

		$fieldsOrder = [];

		$medium->setFieldsOrder($fieldsOrder);
		$medium->setLibraryId($libraryId);

		$medium = $this->mapper->insert($medium);

		if(sizeof($fields) > 0){
			$mediumId = $medium->getId();

			foreach($fields as $field) {
				$fieldEntity = $this->fieldService->create($mediumId, $field["type"], $field["title"], $field["value"]);
				$fieldsOrder[] = $fieldEntity->getId();
			}

			$medium->setFieldsOrder(json_encode($fieldsOrder));

			$medium = $this->mapper->update($medium);
		}

		return $medium;
	}

	public function update(int $id, int $libraryId, string $title, $fieldsOrder) {
		try {
			$medium = $this->mapper->find($id, $libraryId);
			
			if (!is_null($title)) {
				$medium->setTitle($title);
			}
			if (!is_null($fieldsOrder)) {
				$medium->setFieldsOrder($fieldsOrder);
			}

			return $this->mapper->update($medium);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $id, int $libraryId) {
		try {
			$medium = $this->mapper->find($id, $libraryId);
			$this->mapper->delete($mediumj);
			return $medium;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
