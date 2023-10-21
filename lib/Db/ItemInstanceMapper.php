<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class ItemInstanceMapper extends AdvancedQBMapper {
	use ApiObjectMapper;

	const TABLENAME = 'biblio_item_instances';
	const ITEMS_TABLENAME = 'biblio_items';
	const LOANS_TABLE = "biblio_loans";
	const CUSTOMERS_TABLENAME = "biblio_customers";

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
	 * @param string $collectionId
	 * @return array
	 */
	public function findAll(string $collectionId, ?array $filters, ?string $sort = null, ?bool $sortReverse = null, ?int $limit, ?int $offset): array {
		$sortReverse = isset($sortReverse) ? $sortReverse : false;

		if(isset($filters)) {
			//$fieldValueFilters = $this->getFieldValueFilters($filters);
			//$includesFieldValueFilters = (count($fieldValueFilters) !== 0);
		} else {
			$includesFieldValueFilters = false;
		}

		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('instance.*')
			->addSelect($qb->createFunction('COUNT(*) OVER () AS totalResultCount'))
			->from(self::TABLENAME, 'instance');

		$qb->innerJoin('instance', self::ITEMS_TABLENAME, 'item', $qb->expr()->andX(
			$qb->expr()->eq('instance.item_id', 'item.id'),
		))
		->where($qb->expr()->eq('item.collection_id', $qb->createNamedParameter($collectionId)));

		$qb->leftJoin('instance', self::LOANS_TABLE, 'loan', $qb->expr()->andX(
			$qb->expr()->eq('loan.item_instance_id', 'instance.id'),
		));
		
		/*$qb->leftJoin('instance', self::CUSTOMERS_TABLENAME, 'customer', $qb->expr()->andX(
			$qb->expr()->eq('instance.id', 'instance.item_id'),
		));*/

		/*if($includesFieldValueFilters) {
			$qb->innerJoin('instance', self::FIELDS_VALUES_TABLENAME, 'v', $qb->expr()->andX(
					$qb->expr()->eq('instance.id', 'v.item_id'),
					$qb->expr()->in('v.field_id', $qb->createNamedParameter(array_keys($fieldValueFilters), IQueryBuilder::PARAM_INT_ARRAY)),
				))
				->where($qb->expr()->eq('instance.collection_id', $qb->createNamedParameter($collectionId)));

			$validCombinations = $this->getValidFieldValueCombinations($this->db, $qb, $fieldValueFilters, 'v.field_id', 'v.value');

			$qb->andWhere($qb->expr()->orX(...$validCombinations));
		} else {*/
		//}

		$this->handleStringFilter($this->db, $qb, $filters["barcode"], 'instance.barcode');

		/*if($includesFieldValueFilters) {
			$qb->groupBy('instance.id')
    			->having($qb->expr()->eq($qb->createFunction('COUNT(`instance`.`id`)'), $qb->createNamedParameter(count($validCombinations)), IQueryBuilder::PARAM_INT));
		}*/

		if (isset($sort)) {
			if($sort === "barcode") {
				$this->handleSortByColumn($qb, 'instance.barcode', $sortReverse);
			} else if ($this->isFieldReference($sort)) {
				$this->sortByJoinedFieldValue($qb, $sort, $sortReverse, 'i', self::FIELDS_VALUES_TABLENAME, 'item_id');
			}
		}

		$this->handleOffset($qb, $offset);

		$this->handleLimit($qb, $limit);

		return $this->findEntitiesAndSeperateColumns($qb, ["totalResultCount"]);
	}
}
