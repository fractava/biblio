<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

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

			// in order to be able to plug in different storage backends like files
		// for instance it is a good idea to turn storage related exceptions
		// into service related exceptions so controllers and service users
		// have to deal with only one type of exception
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
}
