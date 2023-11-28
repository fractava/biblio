<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class HistoryEntryMapper extends \OCA\Biblio\Db\AdvancedQBMapper {
	use ApiObjectMapper;
	
	public const TABLENAME = 'biblio_history_entries';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, HistoryEntry::class);
	}

	/**
	 * @param int $collectionId
	 * @param string $id
	 * @return Entity|HistoryEntry
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $collectionId, int $id): HistoryEntry {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));
		return $this->findEntity($qb);
	}

	/**
	 * @param int $collectionId
	 * @return array
	 */
	public function findAll(int $collectionId, ?int $subEntryOf = null, ?array $filters, ?string $sort = null, ?bool $sortReverse = null, ?int $limit, ?int $offset): array {
		$sortReverse = isset($sortReverse) ? $sortReverse : false;

		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->addSelect($qb->createFunction('COUNT(*) OVER () AS totalResultCount'))
			->from(self::TABLENAME)
			->where($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));

		if (isset($subEntryOf)) {
			$qb->andWhere($qb->expr()->eq('sub_entry_of', $qb->createNamedParameter($subEntryOf, IQueryBuilder::PARAM_INT)));
		} else {
			$qb->andWhere($qb->expr()->isNull('sub_entry_of'));
		}

		$this->handleNumberFilter($qb, $filters["timestamp"], 'timestamp');
		
		$this->handleIdFilter($qb, $filters["collectionMemberId"], 'collection_member_id');
		$this->handleIdFilter($qb, $filters["itemId"], 'item_id');
		$this->handleIdFilter($qb, $filters["itemFieldId"], 'item_field_id');
		$this->handleIdFilter($qb, $filters["itemFieldValueId"], 'item_field_value_id');
		$this->handleIdFilter($qb, $filters["itemInstanceId"], 'item_instance_id');
		$this->handleIdFilter($qb, $filters["loanId"], 'loan_id');
		$this->handleIdFilter($qb, $filters["loanFieldId"], 'loan_field_id');
		$this->handleIdFilter($qb, $filters["loanFieldValueId"], 'loan_field_value_id');
		$this->handleIdFilter($qb, $filters["customerId"], 'customer_id');
		$this->handleIdFilter($qb, $filters["customerFieldId"], 'customer_field_id');
		$this->handleIdFilter($qb, $filters["customerFieldValueId"], 'customer_field_value_id');

		if (isset($sort)) {
			if ($sort === "timestamp") {
				$this->handleSortByColumn($qb, 'timestamp', $sortReverse);
			}
		}

		$this->handleOffset($qb, $offset);

		$this->handleLimit($qb, $limit);

		return $this->findEntitiesAndSeperateColumns($qb, ["totalResultCount"]);
	}
}
