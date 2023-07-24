<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Medium;
use OCA\Biblio\Db\MediumField;
use OCA\Biblio\Db\MediumFieldMapper;

class MediumFieldService {

	/** @var MediumFieldMapper */
	private $mapper;

	public function __construct(MediumFieldMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(int $mediumId): array {
		return $this->mapper->findAll($mediumId);
	}

	public function findAllInOrder(Medium $entity): array {
		$fieldsOrder = json_decode($entity->getFieldsOrder(), true) ?: [];
		$fields = $this->findAll($entity->getId());

		$fieldsFiltered = [];
		foreach($fields as $field) {
			if(in_array($field->getId(), $fieldsOrder)) {
				$fieldsFiltered[] = $field;
			}
		}

		$cmp = function (MediumField $a, MediumField $b) use ($fieldsOrder): int {
			$pos1 = array_search($a->getId(), $fieldsOrder);
   			$pos2 = array_search($b->getId(), $fieldsOrder);

			if($pos1==$pos2) {
			   return 0;
			} else {
				return ($pos1 < $pos2 ? -1 : 1);
			}
		};

		usort($fieldsFiltered, $cmp);

		return $fieldsFiltered;
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new MediumNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $id, int $mediumId) {
		try {
			return $this->mapper->find($id, $mediumId);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create($mediumId, $type, $title, $value) {
		$field = new MediumField();
        $field->setMediumId($mediumId);
		$field->setType($type);
		$field->setTitle($title);
		$field->setValue($value);
		return $this->mapper->insert($field);
	}

	public function update($id, $mediumId, $type, $title, $value) {
		try {
			$field = $this->mapper->find($id, $mediumId);
			
			if (!is_null($type)) {
				$field->setType($type);
			}
			if (!is_null($title)) {
				$field->setTitle($title);
			}
			if (!is_null($value)) {
				$field->setValue($value);
			}

			return $this->mapper->update($field);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete($id, $mediumId) {
		try {
			$field = $this->mapper->find($id, $mediumId);
			$this->mapper->delete($field);
			return $field;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function findUniqueTitles() {
		return $this->mapper->findUniqueTitles();
	}
}
