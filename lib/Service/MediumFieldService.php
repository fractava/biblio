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

	public function findAll($medium_id): array {
		return $this->mapper->findAll($medium_id);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new MediumNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find($id, $medium_id) {
		try {
			return $this->mapper->find($id, $medium_id);

			// in order to be able to plug in different storage backends like files
		// for instance it is a good idea to turn storage related exceptions
		// into service related exceptions so controllers and service users
		// have to deal with only one type of exception
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create($medium_id, $title, $value) {
		$field = new MediumField();
        $field->setMediumId($medium_id);
		$field->setTitle($title);
		$field->setValue($value);
		return $this->mapper->insert($field);
	}

	public function update($id, $medium_id, $title, $value) {
		try {
			$field = $this->mapper->find($id, $medium_id);
			
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

	public function delete($id, $medium_id) {
		try {
			$field = $this->mapper->find($id, $medium_id);
			$this->mapper->delete($field);
			return $field;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
