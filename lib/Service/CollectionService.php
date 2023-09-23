<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Db\Collection;
use OCA\Biblio\Db\CollectionMapper;

use OCA\Biblio\Db\CollectionMember;
use OCA\Biblio\Db\CollectionMemberMapper;

class CollectionService {

	/** @var CollectionMapper */
	private $mapper;

    /** @var CollectionMemberMapper */
	private $memberMapper;

	public function __construct(CollectionMapper $mapper, CollectionMemberMapper $memberMapper) {
		$this->mapper = $mapper;
        $this->memberMapper = $memberMapper;
	}

	public function findAll(string $userId): array {
		return $this->mapper->findAll($userId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new CollectionNotFound($e->getMessage());
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
		$collection = new Collection();
		$collection->setName($name);

		$collection = $this->mapper->insert($collection);

        $collectionId = $collection->getId();

        $member = new CollectionMember();
        $member->setCollectionId($collectionId);
        $member->setUserId($firstMember);

        $member = $this->memberMapper->insert($member);

		return $collection;
	}

	public function update(int $id, string $name) {
		try {
			$collection = $this->mapper->find($id);
			
			if (!is_null($name)) {
				$collection->setName($name);
			}

			return $this->mapper->update($collection);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $id) {
		try {
			$collection = $this->mapper->find($id);
			$this->mapper->delete($collection);
			return $collection;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
