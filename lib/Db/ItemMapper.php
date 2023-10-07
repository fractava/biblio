<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class ItemMapper extends QBMapper {
	use ApiObjectMapper;
	
	const TABLENAME = 'biblio_items';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, Item::class);
	}

	/**
	 * @param int $id
	 * @param string $userId
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

		$includesFieldValueFilters = false;
		$fieldValueFilters = [];
		if(isset($filters)) {
			foreach ($filters as $key => $value) {
				if(str_starts_with($key, "field:")) {
					$includesFieldValueFilters = true;

					$filterFieldId = substr($key, strlen("field:"));
					if(ctype_digit($filterFieldId)) {
						$fieldValueFilters[(int) $filterFieldId] = $value;
					}
				}
			}
		}

		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		if($includesFieldValueFilters) {
			$qb->select('i.*')
				->from(self::TABLENAME, 'i')
				->innerJoin('i', 'biblio_item_fields_values', 'v', $qb->expr()->andX(
					$qb->expr()->eq('i.id', 'v.item_id'),
					$qb->expr()->in('v.field_id', $qb->createNamedParameter(array_keys($fieldValueFilters), IQueryBuilder::PARAM_INT_ARRAY)),
				))
				->where($qb->expr()->eq('i.collection_id', $qb->createNamedParameter($collectionId)));

			$validCombinations = [];
			foreach ($fieldValueFilters as $key => $value) {
				$validCombinations[] = $qb->expr()->andX(
					$qb->expr()->eq('v.field_id', $qb->createNamedParameter($key), IQueryBuilder::PARAM_INT),
					$this->handleStringFilterExpr($this->db, $qb, $value, 'v.value'),
				);
			}

			$qb->andWhere($qb->expr()->orX(...$validCombinations));

		} else {
			$qb->select('i.*')
				->from(self::TABLENAME, 'i')
				->where($qb->expr()->eq('collection_id', $qb->createNamedParameter($collectionId)));
		}

		$this->handleStringFilter($this->db, $qb, $filters["title"], 'i.title');

		if($includesFieldValueFilters) {
			$qb->groupBy('i.id')
    			->having($qb->expr()->eq($qb->createFunction('COUNT(`i`.`id`)'), $qb->createNamedParameter(count($validCombinations)), IQueryBuilder::PARAM_INT));
		}

		if (isset($sort)) {
			$sortDirection = $sortReverse ? "DESC" : "ASC";

			if($sort === "title") {
				$qb->orderBy('i.title', $sortDirection);
			} else if (str_starts_with($sort, "field:")) {
				$sortFieldId = substr($sort, strlen("field:"));

				if(ctype_digit($sortFieldId)) {
					$sortFieldId = (int) $sortFieldId;

					$qb->leftJoin('i', 'biblio_item_fields_values', 'sort', $qb->expr()->andX(
						$qb->expr()->eq('i.id', 'sort.item_id'),
						$qb->expr()->eq('sort.field_id', $qb->createNamedParameter($sortFieldId), IQueryBuilder::PARAM_INT),
					));

					$qb->orderBy('sort.value', $sortDirection);
				}
			}
		}

		if (isset($offset) && $offset > 0) {
			$qb->setFirstResult($offset);
		}

		if (isset($limit) && $limit > 0) {
			$qb->setMaxResults($limit);
		}

		return $this->findEntities($qb);
	}
}
