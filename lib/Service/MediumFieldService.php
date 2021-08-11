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

	public function create($title, $fields, $userId) {
		$medium = new Medium();
		$medium->setTitle($title);
		$medium->setFields($fields);
		$medium->setUserId($userId);
		return $this->mapper->insert($medium);
	}

	public function update($id, $title, $fields, $userId) {
		try {
			$medium = $this->mapper->find($id, $userId);
			
			if (!is_null($title)) {
				$medium->setTitle($title);
			}
			if (!is_null($fields)) {
				$medium->setFields($fields);
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
