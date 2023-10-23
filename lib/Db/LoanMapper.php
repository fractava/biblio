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

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, Loan::class);
	}

	/**
	 * @param int $id
	 * @return Entity|Loan
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): Loan {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntity($qb);
	}

	/**
	 * @param string $barcode
	 * @return Entity|Loan
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function findByItemInstanceBarcode(string $barcode): Loan {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('loan.*')
			->from(self::TABLENAME, 'loan')
			->innerJoin('loan', self::ITEM_INSTANCES_TABLENAME, 'instance', $qb->expr()->andX(
				$qb->expr()->eq('loan.item_instance_id', 'instance.id'),
				$qb->expr()->eq('instance.barcode', $qb->createNamedParameter($barcode))
			));
		
		return $this->findEntity($qb);
	}

	/**
	 * @param int $itemId
	 * @return array
	 */
	public function findAllOfCustomer(string $customerId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('customer_id', $qb->createNamedParameter($customerId)));
		
		return $this->findEntities($qb);
	}
}
