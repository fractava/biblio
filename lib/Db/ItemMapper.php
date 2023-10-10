<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class ItemMapper extends \OCA\Biblio\Db\AdvancedQBMapper {
	use ApiObjectMapper;
	
	const TABLENAME = 'biblio_items';
	const FIELDS_VALUES_TABLENAME = 'biblio_item_fields_values';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, Item::class);
	}

	/**
	 * @param int $collectionId
	 * @param string $id
	 * @return Entity|Item
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $collectionId, int $id): Item {
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

		if(isset($filters)) {
			$fieldValueFilters = $this->getFieldValueFilters($filters);
			$includesFieldValueFilters = (count($fieldValueFilters) !== 0);
		} else {
			$includesFieldValueFilters = false;
		}

		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('i.*')
			->addSelect($qb->createFunction('COUNT(*) OVER () AS totalResultCount'))
			->from(self::TABLENAME, 'i');

		if($includesFieldValueFilters) {
			$qb->innerJoin('i', self::FIELDS_VALUES_TABLENAME, 'v', $qb->expr()->andX(
					$qb->expr()->eq('i.id', 'v.item_id'),
					$qb->expr()->in('v.field_id', $qb->createNamedParameter(array_keys($fieldValueFilters), IQueryBuilder::PARAM_INT_ARRAY)),
				))
				->where($qb->expr()->eq('i.collection_id', $qb->createNamedParameter($collectionId)));

			$validCombinations = $this->getValidFieldValueCombinations($this->db, $qb, $fieldValueFilters, 'v.field_id', 'v.value');

			$qb->andWhere($qb->expr()->orX(...$validCombinations));
		} else {
			$qb->where($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId)));
		}

		$this->handleStringFilter($this->db, $qb, $filters["title"], 'i.title');

		if($includesFieldValueFilters) {
			$qb->groupBy('i.id')
    			->having($qb->expr()->eq($qb->createFunction('COUNT(`i`.`id`)'), $qb->createNamedParameter(count($validCombinations)), IQueryBuilder::PARAM_INT));
		}

		if (isset($sort)) {
			if($sort === "title") {
				$this->handleSortByColumn($qb, 'i.title', $sortReverse);
			} else if ($this->isFieldReference($sort)) {
				$this->sortByJoinedFieldValue($qb, $sort, $sortReverse, 'i', self::FIELDS_VALUES_TABLENAME, 'item_id');
			}
		}

		$this->handleOffset($qb, $offset);

		$this->handleLimit($qb, $limit);

		return $this->findEntitiesAndSeperateColumns($qb, ["totalResultCount"]);
	}
}
