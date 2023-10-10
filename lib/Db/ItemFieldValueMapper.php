<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class ItemFieldValueMapper extends QBMapper {
	use ApiObjectMapper;

	const TABLENAME = 'biblio_item_fields_values';

	const FIELDS_TABLENAME = 'biblio_item_fields';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, ItemFieldValue::class);
	}

	/**
	 * @param array $parameters
	 * @return Entity|ItemFieldValue
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(array $parameters): ItemFieldValue {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$findById = array_key_exists("id", $parameters);
		$findByItemIdFieldId = array_key_exists("itemId", $parameters) && array_key_exists("fieldId", $parameters);

		$qb->select('*')
			->from(self::TABLENAME);

		if ($findById) {
			$qb->where($qb->expr()->eq('id', $qb->createNamedParameter($parameters["id"], IQueryBuilder::PARAM_INT)));
		} else if ($findByItemIdFieldId) {
			$qb->where($qb->expr()->eq('item_id', $qb->createNamedParameter($parameters["itemId"], IQueryBuilder::PARAM_INT)))
            	->andWhere($qb->expr()->eq('field_id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)));
		} else {
			throw new Exception("Invalid parameters supplied to ItemFieldValueMapper->find");
		}

		return $this->findEntity($qb);
	}

	/**
	 * @param array $parameters
	 * @return ItemFieldValueFieldCombination
	 */
	public function findIncludingField(array $parameters): ItemFieldValueFieldCombination {
		if (array_key_exists("collectionId", $parameters)) {
			/* @var $qb IQueryBuilder */
			$qb = $this->db->getQueryBuilder();

			$qb->select('*')
				->addSelect("f.id AS field_id")
				->from(self::TABLENAME, 'v');

			$findById = array_key_exists("id", $parameters);
			$findByItemIdFieldId = array_key_exists("itemId", $parameters) && array_key_exists("fieldId", $parameters);

			if ($findById) {
				$joinON = [
					$qb->expr()->eq('v.id', $qb->createNamedParameter($parameters["id"], IQueryBuilder::PARAM_INT)),
					$qb->expr()->eq('v.field_id', 'f.id'),
				];
			} else if ($findByItemIdFieldId) {
				$joinON = [
					$qb->expr()->eq('v.item_id', $qb->createNamedParameter($parameters["itemId"], IQueryBuilder::PARAM_INT)),
					$qb->expr()->eq('v.field_id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)),
				];
			} else {
				throw new Exception("Invalid parameters supplied to ItemFieldValueMapper->findIncludingField");
			}
			
			$qb->rightJoin('v', self::FIELDS_TABLENAME, 'f', $qb->expr()->andX(...$joinON));

			$collectionExpr = $qb->expr()->eq('f.collection_id', $qb->createNamedParameter($parameters["collectionId"], IQueryBuilder::PARAM_INT));

			if ($findById) {
				$qb->where($collectionExpr);
			} else if ($findByItemIdFieldId) {
				$qb->where($qb->expr()->eq('f.id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)));
				$qb->andWhere($collectionExpr);
			}

			$result = ItemFieldValueFieldCombination::fromRow($this->findOneQuery($qb));
			
			return $result;
		} else {
			throw new Exception("Invalid parameters supplied to ItemFieldValueMapper->findIncludingField");
		}
	}

	/**
	 * @param int $itemId
	 * @return array
	 */
	public function findAll(int $itemId, ?array $filters): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('item_id', $qb->createNamedParameter($itemId, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntities($qb);
	}

    /**
	 * @param int $itemId
	 * @return array
	 */
	public function findAllIncludingFields(int $collectionId, int $itemId, ?array $filters, ?int $limit = null, ?int $offset = null): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
        $qb->select('*')
			->addSelect("f.id AS field_id")
            ->from(self::TABLENAME, 'v')
            ->rightJoin('v', self::FIELDS_TABLENAME, 'f', $qb->expr()->andX(
                $qb->expr()->eq('v.field_id', 'f.id'),
                $qb->expr()->eq('v.item_id', $qb->createNamedParameter($itemId, IQueryBuilder::PARAM_INT))
            ))
			->where($qb->expr()->eq('f.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));

		$this->handleBooleanFilter($qb, $filters["includeInList"], 'f.include_in_list');
		$this->handleStringFilter($this->db, $qb, $filters["value"], 'v.value');

		if (isset($offset)) {
			$qb->setFirstResult($offset);
		}

		if (isset($limit)) {
			$qb->setMaxResults($limit);
		}
		
		$result = $qb->executeQuery();
		try {
			$entities = [];
			while ($row = $result->fetch()) {
				$entities[] = ItemFieldValueFieldCombination::fromRow($row);
			}
			return $entities;
		} finally {
			$result->closeCursor();
		}
        
		return $entities;
	}
}
