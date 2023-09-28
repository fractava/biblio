<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ItemFieldValueMapper extends QBMapper {
	const TABLENAME = 'biblio_item_fields_values';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, ItemFieldValue::class);
	}

	/**
	 * @param int $id
	 * @return Entity|ItemFieldValue
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): ItemFieldValue {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntity($qb);
	}

    /**
	 * @param int $itemId
     * @param int $fieldId
	 * @return Entity|ItemFieldValue
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function findByItemAndFieldId(int $itemId, int $fieldId): ItemFieldValue {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('item_id', $qb->createNamedParameter($itemId, IQueryBuilder::PARAM_INT)))
            ->andWhere($qb->expr()->eq('field_id', $qb->createNamedParameter($fieldId, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntity($qb);
	}

	/**
	 * @param int $itemId
	 * @return ItemFieldValueFieldCombination
	 */
	public function findByItemAndFieldIdIncludingField(int $collectionId, int $itemId, int $fieldId): ItemFieldValueFieldCombination {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
        $qb->select('*')
			->addSelect("f.id AS field_id")
            ->from(self::TABLENAME, 'v')
            ->rightJoin('v', 'biblio_item_fields', 'f', $qb->expr()->andX(
				$qb->expr()->eq('v.item_id', $qb->createNamedParameter($itemId, IQueryBuilder::PARAM_INT)),
                $qb->expr()->eq('v.field_id', $qb->createNamedParameter($fieldId, IQueryBuilder::PARAM_INT)),
			))
			->where($qb->expr()->eq('f.id', $qb->createNamedParameter($fieldId, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('f.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));
		
        $result = ItemFieldValueFieldCombination::fromRow($this->findOneQuery($qb));
        
		return $result;
	}

	/**
	 * @param int $itemId
	 * @return array
	 */
	public function findAll(int $itemId): array {
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
	public function findAllIncludingFields(int $collectionId, int $itemId, bool $onlyIncludedInList = false): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
        $qb->select('*')
			->addSelect("f.id AS field_id")
            ->from(self::TABLENAME, 'v')
            ->rightJoin('v', 'biblio_item_fields', 'f', $qb->expr()->andX(
                $qb->expr()->eq('v.field_id', 'f.id'),
                $qb->expr()->eq('v.item_id', $qb->createNamedParameter($itemId, IQueryBuilder::PARAM_INT))
            ))
			->where($qb->expr()->eq('f.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));

		if($onlyIncludedInList) {
			$qb->andWhere($qb->expr()->eq('f.include_in_list', $qb->createNamedParameter(true, IQueryBuilder::PARAM_BOOL)));
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
