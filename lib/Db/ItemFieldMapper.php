<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ItemFieldMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'biblio_item_fields', ItemField::class);
	}

	/**
	 * @param int $id
	 * @param int $itemId
	 * @return Entity|ItemField
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id, int $itemId): ItemField {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('biblio_item_fields')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('item_id', $qb->createNamedParameter($itemId, IQueryBuilder::PARAM_INT)));
		return $this->findEntity($qb);
	}

	/**
	 * @param int $itemId
	 * @return array
	 */
	public function findAll(int $itemId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('biblio_item_fields')
			->where($qb->expr()->eq('item_id', $qb->createNamedParameter($itemId, IQueryBuilder::PARAM_INT)));
		return $this->findEntities($qb);
	}

	/**
	 * @return array
	 */
	public function findUniqueTitles(): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->selectDistinct('title')->from('biblio_item_fields');
		$result = $qb->executeQuery();

		return array_column($result->fetchAll(), "title");
	}
}
