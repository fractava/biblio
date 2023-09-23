<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class MediumMapper extends QBMapper {
	const TABLENAME = 'biblio_mediums';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, Medium::class);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Entity|Medium
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id, int $collectionId): Medium {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId)));
		return $this->findEntity($qb);
	}

	/**
	 * @param string $collectionId
	 * @return array
	 */
	public function findAll(string $collectionId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId)));
		return $this->findEntities($qb);
	}
}
