<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ItemInstanceMapper extends QBMapper {
	const TABLENAME = 'biblio_item_instances';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, ItemInstance::class);
	}

	/**
	 * @param int $id
	 * @return Entity|ItemInstance
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): ItemInstance {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        
		return $this->findEntity($qb);
	}

	/**
	 * @param string $barcode
	 * @return Entity|ItemInstance
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function findByBarcode(string $barcode): ItemInstance {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('barcode', $qb->createNamedParameter($barcode)));
        
		return $this->findEntity($qb);
	}

	/**
	 * @param int $itemId
	 * @return array
	 */
	public function findAll(string $itemId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('item_id', $qb->createNamedParameter($itemId)));
        
		return $this->findEntities($qb);
	}
}
