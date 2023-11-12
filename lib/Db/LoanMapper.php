<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class LoanMapper extends QBMapper {
	public const TABLENAME = 'biblio_loans';
	public const ITEM_INSTANCES_TABLENAME = 'biblio_item_instances';
	public const ITEMS_TABLENAME = 'biblio_items';
	public const CUSTOMERS_TABLENAME = "biblio_customers";

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, Loan::class);
	}

	/**
	 * @param int $id
	 * @return Entity|Loan
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $collectionId, int $id): Loan {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('loan.*')
			->from(self::TABLENAME, 'loan');

		$qb->innerJoin('loan', self::ITEM_INSTANCES_TABLENAME, 'instance', $qb->expr()->andX(
			$qb->expr()->eq('loan.item_instance_id', 'instance.id'),
		));

		$qb->innerJoin('instance', self::ITEMS_TABLENAME, 'item', $qb->expr()->andX(
			$qb->expr()->eq('instance.item_id', 'item.id'),
		));

		$qb->innerJoin('loan', self::CUSTOMERS_TABLENAME, 'customer', $qb->expr()->andX(
			$qb->expr()->eq('loan.customer_id', 'customer.id'),
		));

		$qb->where($qb->expr()->eq('loan.id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('item.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('customer.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntity($qb);
	}

	/**
	 * @param string $barcode
	 * @return Entity|Loan
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function findByItemInstanceBarcode(int $collectionId, string $barcode): Loan {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('loan.*')
			->from(self::TABLENAME, 'loan');

		$qb->innerJoin('loan', self::ITEM_INSTANCES_TABLENAME, 'instance', $qb->expr()->andX(
			$qb->expr()->eq('loan.item_instance_id', 'instance.id'),
		));

		$qb->innerJoin('instance', self::ITEMS_TABLENAME, 'item', $qb->expr()->andX(
			$qb->expr()->eq('instance.item_id', 'item.id'),
		));
 
		$qb->innerJoin('loan', self::CUSTOMERS_TABLENAME, 'customer', $qb->expr()->andX(
			$qb->expr()->eq('loan.customer_id', 'customer.id'),
		));

		$qb->where($qb->expr()->eq('instance.barcode', $qb->createNamedParameter($barcode)))
			->andWhere($qb->expr()->eq('item.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('customer.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntity($qb);
	}

	/**
	 * @param int $itemId
	 * @return array
	 */
	public function findAllOfCustomer(int $customerId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('customer_id', $qb->createNamedParameter($customerId, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntities($qb);
	}

	/**
	 * @param int $collectionId
	 * @return array
	 */
	public function findAllOfCollection(int $collectionId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('loan.*')
			->from(self::TABLENAME, 'loan')
			->innerJoin('loan', self::ITEM_INSTANCES_TABLENAME, 'instance', $qb->expr()->eq('loan.item_instance_id', 'instance.id'))
			->innerJoin('instance', self::ITEM_INSTANCES_TABLENAME, 'item', $qb->expr()->andX(
				$qb->expr()->eq('instance.itemId', 'item.id'),
				$qb->expr()->eq('item.collectionId', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT))
			));
		return $this->findEntities($qb);
	}
}
