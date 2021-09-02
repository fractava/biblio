<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Medium;
use OCA\Biblio\Db\MediumMapper;

class MediumService {

	/** @var MediumMapper */
	private $mapper;

	public function __construct(MediumMapper $mapper) {
		$this->mapper = $mapper;
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

			// in order to be able to plug in different storage backends like files
		// for instance it is a good idea to turn storage related exceptions
		// into service related exceptions so controllers and service users
		// have to deal with only one type of exception
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create($title, $fields_order, $userId) {
		$medium = new Medium();
		$medium->setTitle($title);
		$medium->setFieldsOrder($fields_order);
		$medium->setUserId($userId);
		return $this->mapper->insert($medium);
	}

	public function update($id, $title, $fields_order, $userId) {
		try {
			$medium = $this->mapper->find($id, $userId);
			
			if (!is_null($title)) {
				$medium->setTitle($title);
			}
			if (!is_null($fields_order)) {
				$medium->setFieldsOrder($fields_order);
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
