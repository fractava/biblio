<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Library;
use OCA\Biblio\Db\LibraryMapper;

use OCA\Biblio\Db\LibraryMember;
use OCA\Biblio\Db\LibraryMemberMapper;

class LibraryService {

	/** @var LibraryMapper */
	private $mapper;

    /** @var LibraryMemberMapper */
	private $memberMapper;

	public function __construct(LibraryMapper $mapper, LibraryMemberMapper $memberMapper) {
		$this->mapper = $mapper;
        $this->memberMapper = $memberMapper;
	}

	public function findAll(string $userId): array {
		return $this->mapper->findAll($userId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new LibraryNotFound($e->getMessage());
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

	public function create(string $name, string $firstMember) {
		$library = new Library();
		$library->setName($name);

		$library = $this->mapper->insert($library);

        $libraryId = $library->getId();

        $member = new LibraryMember();
        $member->setLibraryId($libraryId);
        $member->setUserId($firstMember);

        $member = $this->memberMapper->insert($member);

		return $library;
	}

	public function update(int $id, string $name) {
		try {
			$library = $this->mapper->find($id);
			
			if (!is_null($name)) {
				$library->setName($name);
			}

			return $this->mapper->update($library);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $id) {
		try {
			$library = $this->mapper->find($id);
			$this->mapper->delete($library);
			return $library;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
