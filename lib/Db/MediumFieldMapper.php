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
	 * @return Entity|MediumField
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id, int $mediumId): MediumField {
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

	/**
	 * @return array
	 */
	public function findUniqueTitles(): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->selectDistinct('title')->from('biblio_medium_fields');
		$result = $qb->executeQuery();

		return array_column($result->fetchAll(), "title");
	}
}
