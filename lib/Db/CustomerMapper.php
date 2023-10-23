<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class CustomerMapper extends \OCA\Biblio\Db\AdvancedQBMapper {
	use ApiObjectMapper;
	
	public const TABLENAME = 'biblio_customers';
	public const FIELDS_VALUES_TABLENAME = 'biblio_customer_fields_values';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, Customer::class);
	}

	/**
	 * @param int $collectionId
	 * @param string $id
	 * @return Entity|Customer
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $collectionId, int $id): Customer {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));
		return $this->findEntity($qb);
	}

	/**
	 * @param string $collectionId
	 * @return array
	 */
	public function findAll(string $collectionId, ?array $filters, ?string $sort = null, ?bool $sortReverse = null, ?int $limit, ?int $offset): array {
		$sortReverse = isset($sortReverse) ? $sortReverse : false;

		if (isset($filters)) {
			$fieldValueFilters = $this->getFieldValueFilters($filters);
			$includesFieldValueFilters = (count($fieldValueFilters) !== 0);
		} else {
			$includesFieldValueFilters = false;
		}

		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('c.*')
			->addSelect($qb->createFunction('COUNT(*) OVER () AS totalResultCount'))
			->from(self::TABLENAME, 'c');

		if ($includesFieldValueFilters) {
			$qb->innerJoin('c', self::FIELDS_VALUES_TABLENAME, 'v', $qb->expr()->andX(
				$qb->expr()->eq('c.id', 'v.customer_id'),
				$qb->expr()->in('v.field_id', $qb->createNamedParameter(array_keys($fieldValueFilters), IQueryBuilder::PARAM_INT_ARRAY)),
			))
				->where($qb->expr()->eq('c.collection_id', $qb->createNamedParameter($collectionId)));

			$validCombinations = $this->getValidFieldValueCombinations($this->db, $qb, $fieldValueFilters, 'v.field_id', 'v.value');

			$qb->andWhere($qb->expr()->orX(...$validCombinations));
		} else {
			$qb->where($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId)));
		}

		$this->handleStringFilter($this->db, $qb, $filters["name"], 'c.name');

		if ($includesFieldValueFilters) {
			$qb->groupBy('c.id')
				->having($qb->expr()->eq($qb->createFunction('COUNT(`c`.`id`)'), $qb->createNamedParameter(count($validCombinations)), IQueryBuilder::PARAM_INT));
		}

		if (isset($sort)) {
			if ($sort === "name") {
				$this->handleSortByColumn($qb, 'c.name', $sortReverse);
			} elseif ($this->isFieldReference($sort)) {
				$this->sortByJoinedFieldValue($qb, $sort, $sortReverse, 'c', self::FIELDS_VALUES_TABLENAME, 'customer_id');
			}
		}

		$this->handleOffset($qb, $offset);

		$this->handleLimit($qb, $limit);

		return $this->findEntitiesAndSeperateColumns($qb, ["totalResultCount"]);
	}
}
