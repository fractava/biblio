<?php

namespace OCA\Biblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Biblio\Errors\CollectionMemberNotFound;

use OCA\Biblio\Db\CollectionMember;
use OCA\Biblio\Db\CollectionMemberMapper;

class CollectionMemberService {
	/** @var CollectionMemberMapper */
	private $mapper;

	public function __construct(CollectionMemberMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(string $collectionId): array {
		return $this->mapper->findAll($collectionId);
	}

	public function findAllByUser(string $userId) {
		return $this->mapper->findAllByUser($userId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new CollectionMemberNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(int $collectionId, int $id) {
		try {
			return $this->mapper->find($collectionId, $id);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(int $collectionId, string $userId) {
		$member = new CollectionMember();
		$member->setCollectionId($collectionId);
		$member->setUserId($userId);

		return $this->mapper->insert($member);
	}

	public function update(int $collectionId, int $id) {
		try {
			$member = $this->mapper->find($collectionId, $id);
			
			// TODO: implement permissions management

			return $this->mapper->update($member);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete(int $collectionId, int $id) {
		try {
			$member = $this->mapper->find($collectionId, $id);
			$this->mapper->delete($member);
			return $member;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
