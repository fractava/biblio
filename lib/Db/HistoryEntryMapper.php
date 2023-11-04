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
	public function findAll(int $collectionId, ?array $filters, ?string $sort = null, ?bool $sortReverse = null, ?int $limit, ?int $offset): array {
		$sortReverse = isset($sortReverse) ? $sortReverse : false;

		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->addSelect($qb->createFunction('COUNT(*) OVER () AS totalResultCount'))
			->from(self::TABLENAME)
			->where($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));
		
		$this->handleIdFilter($qb, $filters["collectionMemberId"], 'collectionMemberId');
		$this->handleIdFilter($qb, $filters["itemId"], 'itemId');
		$this->handleIdFilter($qb, $filters["itemFieldId"], 'itemFieldId');
		$this->handleIdFilter($qb, $filters["itemFieldValueId"], 'itemFieldValueId');
		$this->handleIdFilter($qb, $filters["loanId"], 'loanId');
		$this->handleIdFilter($qb, $filters["loanFieldId"], 'loanFieldId');
		$this->handleIdFilter($qb, $filters["loanFieldValueId"], 'loanFieldValueId');
		$this->handleIdFilter($qb, $filters["customerId"], 'customerId');
		$this->handleIdFilter($qb, $filters["customerFieldId"], 'customerFieldId');
		$this->handleIdFilter($qb, $filters["customerFieldValueId"], 'customerFieldValueId');

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
