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

	public function findAll(string $userId): array {
		return $this->mapper->findAll($userId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new MediumNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find($id, $userId) {
		try {
			return $this->mapper->find($id, $userId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(string $title, array $fields, string $userId) {
		$medium = new Medium();
		$medium->setTitle($title);

		$fieldsOrder = [];

		$medium->setFieldsOrder($fieldsOrder);
		$medium->setUserId($userId);

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

	public function update($id, $title, $fieldsOrder, $userId) {
		try {
			$medium = $this->mapper->find($id, $userId);
			
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

	public function delete($id, $userId) {
		try {
			$medium = $this->mapper->find($id, $userId);
			$this->mapper->delete($mediumj);
			return $medium;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
