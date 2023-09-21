<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class LibraryMemberMapper extends QBMapper {
    const TABLENAME = 'biblio_library_members';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, LibraryMember::class);
	}

	/**
	 * @param int $id
	 * @return Entity|LibraryMember
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): LibraryMember {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        
		return $this->findEntity($qb);
	}

	/**
	 * @param int $libraryId
	 * @return array
	 */
	public function findAll(int $libraryId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('library_id', $qb->createNamedParameter($libraryId, IQueryBuilder::PARAM_INT)));
        
		return $this->findEntities($qb);
	}

    /**
	 * @param string $userId
	 * @return array
	 */
	public function findAllByUser(string $userId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
		return $this->findEntities($qb);
	}
}
