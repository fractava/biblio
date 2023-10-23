<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class ItemInstanceMapper extends AdvancedQBMapper {
	use ApiObjectMapper;

	public const TABLENAME = 'biblio_item_instances';
	public const ITEMS_TABLENAME = 'biblio_items';
	public const LOANS_TABLE = "biblio_loans";
	public const CUSTOMERS_TABLENAME = "biblio_customers";

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
	 * Runs a sql query and returns an array of entities
	 *
	 * @param IQueryBuilder $query
	 * @param array $ignoredColumns
	 * @return array all fetched entities and all seperate columns
	 * @psalm-return T[] all fetched entities and all seperate columns
	 * @throws Exception
	 * @since 14.0.0
	 */
	protected function findEntitiesAndSeperateColumns(IQueryBuilder $query, array $seperateColumns): array {
		$result = $query->executeQuery();

		$seperateColumnResults = [];

		try {
			$entities = [];
			$firstIteration = true;
			while ($row = $result->fetch()) {
				if ($firstIteration) {
					$firstIteration = false;
					foreach ($seperateColumns as $column) {
						$seperateColumnResults[$column] = $row[$column];
					}
				}

				$filteredRow = array_diff_key($row, array_flip($seperateColumns));

				$itemInstanceColumns = [
					"id" => $filteredRow["id"],
					"barcode" => $filteredRow["barcode"],
					"item_id" => $filteredRow["item_id"],
				];

				$itemColumns = [
					"id" => $filteredRow["itemJoin_id"],
					"collection_id" => $filteredRow["itemJoin_collection_id"],
					"title" => $filteredRow["itemJoin_title"],
				];

				$loanColumns = [
					"id" => $filteredRow["loanJoin_id"],
					"item_instance_id" => $filteredRow["loanJoin_item_instance_id"],
					"customer_id" => $filteredRow["loanJoin_customer_id"],
					"until" => $filteredRow["loanJoin_until"],
				];

				$loanCustomerColumns = [
					"id" => $filteredRow["loanCustomerJoin_id"],
					"collection_id" => $filteredRow["loanCustomerJoin_collection_id"],
					"name" => $filteredRow["loanCustomerJoin_name"],
				];

				$entities[] = [
					"instance" => \call_user_func(ItemInstance::class .'::fromRow', $itemInstanceColumns),
					"item" => \call_user_func(Item::class .'::fromRow', $itemColumns),
					"loan" => \call_user_func(Loan::class .'::fromRow', $loanColumns),
					"loanCustomer" => \call_user_func(Customer::class .'::fromRow', $loanCustomerColumns),
				];
			}
			return [$entities, $seperateColumnResults];
		} finally {
			$result->closeCursor();
		}
	}

	/**
	 * @param string $collectionId
	 * @return array
	 */
	public function findAll(string $collectionId, ?array $filters, ?string $sort = null, ?bool $sortReverse = null, ?int $limit, ?int $offset): array {
		$sortReverse = isset($sortReverse) ? $sortReverse : false;

		if (isset($filters)) {
			//$fieldValueFilters = $this->getFieldValueFilters($filters);
			//$includesFieldValueFilters = (count($fieldValueFilters) !== 0);
		} else {
			$includesFieldValueFilters = false;
		}

		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('instance.*')
			->addSelect($qb->createFunction('COUNT(*) OVER () AS totalResultCount'))
			->addSelect($qb->createFunction('item.id AS itemJoin_id'))
			->addSelect($qb->createFunction('item.collection_id AS itemJoin_collection_id'))
			->addSelect($qb->createFunction('item.title AS itemJoin_title'))
			->addSelect($qb->createFunction('loan.id AS loanJoin_id'))
			->addSelect($qb->createFunction('loan.item_instance_id AS loanJoin_item_instance_id'))
			->addSelect($qb->createFunction('loan.customer_id AS loanJoin_customer_id'))
			->addSelect($qb->createFunction('loan.until AS loanJoin_until'))
			->addSelect($qb->createFunction('loanCustomer.id AS loanCustomerJoin_id'))
			->addSelect($qb->createFunction('loanCustomer.collection_id AS loanCustomerJoin_collection_id'))
			->addSelect($qb->createFunction('loanCustomer.name AS loanCustomerJoin_name'))
			->from(self::TABLENAME, 'instance');

		$qb->innerJoin('instance', self::ITEMS_TABLENAME, 'item', $qb->expr()->andX(
			$qb->expr()->eq('instance.item_id', 'item.id'),
		))
		->where($qb->expr()->eq('item.collection_id', $qb->createNamedParameter($collectionId)));

		$qb->leftJoin('instance', self::LOANS_TABLE, 'loan', $qb->expr()->andX(
			$qb->expr()->eq('loan.item_instance_id', 'instance.id'),
		));

		$qb->leftJoin('instance', self::CUSTOMERS_TABLENAME, 'loanCustomer', $qb->expr()->andX(
			$qb->expr()->eq('loanCustomer.id', 'loan.customer_id'),
		));

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

		$this->handleStringFilter($this->db, $qb, $filters["item_title"], 'item.title');
		
		$this->handleStringFilter($this->db, $qb, $filters["loan_customer_name"], 'loanCustomer.name');

		$this->handleIdFilter($qb, $filters["item_id"], 'instance.item_id');

		/*if($includesFieldValueFilters) {
			$qb->groupBy('instance.id')
				->having($qb->expr()->eq($qb->createFunction('COUNT(`instance`.`id`)'), $qb->createNamedParameter(count($validCombinations)), IQueryBuilder::PARAM_INT));
		}*/

		if (isset($sort)) {
			if ($sort === "barcode") {
				$this->handleSortByColumn($qb, 'instance.barcode', $sortReverse);
			} elseif ($sort === "item_title") {
				$this->handleSortByColumn($qb, 'item.title', $sortReverse);
			} elseif ($sort === "loan_customer_name") {
				$this->handleSortByColumn($qb, 'loanCustomer.name', $sortReverse);
			} elseif ($this->isFieldReference($sort)) {
				$this->sortByJoinedFieldValue($qb, $sort, $sortReverse, 'i', self::FIELDS_VALUES_TABLENAME, 'item_id');
			}
		}

		$this->handleOffset($qb, $offset);

		$this->handleLimit($qb, $limit);

		return $this->findEntitiesAndSeperateColumns($qb, ["totalResultCount"]);
	}
}
