<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class CollectionMapper extends QBMapper {
    const TABLENAME = 'biblio_collections';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, Collection::class);
	}

	/**
	 * @param int $id
	 * @return Entity|Collection
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): Collection {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        
		return $this->findEntity($qb);
	}

	/**
	 * @param string $userId
	 * @return array
	 */
	public function findAll(string $userId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

        $qb->select('l.*')
            ->from(self::TABLENAME, 'l')
            ->innerJoin('l', 'biblio_collection_members', 'm', $qb->expr()->andX(
                $qb->expr()->eq('m.collection_id', 'l.id'),
                $qb->expr()->eq('m.user_id', $qb->createNamedParameter($userId))
            ));
        
		return $this->findEntities($qb);
	}
}
