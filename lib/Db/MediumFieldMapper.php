<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class MediumFieldMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'biblio_medium_fields', MediumField::class);
	}

	/**
	 * @param int $id
	 * @param int $mediumId
	 * @return Entity|Medium
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id, int $mediumId): Medium {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('biblio_medium_fields')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('medium_id', $qb->createNamedParameter($mediumId, IQueryBuilder::PARAM_INT)));
		return $this->findEntity($qb);
	}

	/**
	 * @param int $mediumId
	 * @return array
	 */
	public function findAll(int $mediumId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('biblio_medium_fields')
			->where($qb->expr()->eq('medium_id', $qb->createNamedParameter($mediumId, IQueryBuilder::PARAM_INT)));
		return $this->findEntities($qb);
	}
}
