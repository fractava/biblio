<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class CustomerFieldValueMapper extends QBMapper {
	use ApiObjectMapper;

	public const TABLENAME = 'biblio_customer_fields_values';

	public const FIELDS_TABLENAME = 'biblio_customer_fields';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, CustomerFieldValue::class);
	}

	/**
	 * @param array $parameters
	 * @return Entity|CustomerFieldValue
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(array $parameters): CustomerFieldValue {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$findById = array_key_exists("id", $parameters);
		$findByCustomerIdFieldId = array_key_exists("customerId", $parameters) && array_key_exists("fieldId", $parameters);

		$qb->select('*')
			->from(self::TABLENAME);

		if ($findById) {
			$qb->where($qb->expr()->eq('id', $qb->createNamedParameter($parameters["id"], IQueryBuilder::PARAM_INT)));
		} elseif ($findByCustomerIdFieldId) {
			$qb->where($qb->expr()->eq('customer_id', $qb->createNamedParameter($parameters["customerId"], IQueryBuilder::PARAM_INT)))
				->andWhere($qb->expr()->eq('field_id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)));
		} else {
			throw new Exception("Invalid parameters supplied to CustomerFieldValueMapper->find");
		}

		return $this->findEntity($qb);
	}

	/**
	 * @param array $parameters
	 * @return CustomerFieldValueFieldCombination
	 */
	public function findIncludingField(array $parameters): CustomerFieldValueFieldCombination {
		if (array_key_exists("collectionId", $parameters)) {
			/* @var $qb IQueryBuilder */
			$qb = $this->db->getQueryBuilder();

			$qb->select('*')
				->addSelect("f.id AS field_id")
				->from(self::FIELDS_TABLENAME, 'f');

			$findById = array_key_exists("id", $parameters);
			$findByCustomerIdFieldId = array_key_exists("customerId", $parameters) && array_key_exists("fieldId", $parameters);

			if ($findById) {
				$joinON = [
					$qb->expr()->eq('v.id', $qb->createNamedParameter($parameters["id"], IQueryBuilder::PARAM_INT)),
					$qb->expr()->eq('v.field_id', 'f.id'),
				];
			} elseif ($findByCustomerIdFieldId) {
				$joinON = [
					$qb->expr()->eq('v.customer_id', $qb->createNamedParameter($parameters["customerId"], IQueryBuilder::PARAM_INT)),
					$qb->expr()->eq('v.field_id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)),
				];
			} else {
				throw new Exception("Invalid parameters supplied to CustomerFieldValueMapper->findIncludingField");
			}
			
			$qb->leftJoin('f', self::TABLENAME, 'v', $qb->expr()->andX(...$joinON));

			$collectionExpr = $qb->expr()->eq('f.collection_id', $qb->createNamedParameter($parameters["collectionId"], IQueryBuilder::PARAM_INT));

			if ($findById) {
				$qb->where($collectionExpr);
			} elseif ($findByCustomerIdFieldId) {
				$qb->where($qb->expr()->eq('f.id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)));
				$qb->andWhere($collectionExpr);
			}

			$result = CustomerFieldValueFieldCombination::fromRow($this->findOneQuery($qb));
			
			return $result;
		} else {
			throw new Exception("Invalid parameters supplied to CustomerFieldValueMapper->findIncludingField");
		}
	}

	/**
	 * @param int $customerId
	 * @return array
	 */
	public function findAll(int $customerId, ?array $filters): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('customer_id', $qb->createNamedParameter($customerId, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntities($qb);
	}

	/**
	 * @param int $customerId
	 * @return array
	 */
	public function findAllIncludingFields(int $collectionId, int $customerId, ?array $filters, ?int $limit = null, ?int $offset = null): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->addSelect("f.id AS field_id")
			->from(self::FIELDS_TABLENAME, 'f')
			->leftJoin('f', self::TABLENAME, 'v', $qb->expr()->andX(
				$qb->expr()->eq('v.field_id', 'f.id'),
				$qb->expr()->eq('v.customer_id', $qb->createNamedParameter($customerId, IQueryBuilder::PARAM_INT))
			))
			->where($qb->expr()->eq('f.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));

		$this->handleBooleanFilter($qb, $filters["includeInList"], 'f.include_in_list');
		$this->handleJsonStringFilter($this->db, $qb, $filters["value"], 'v.value');

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
				$entities[] = CustomerFieldValueFieldCombination::fromRow($row);
			}
			return $entities;
		} finally {
			$result->closeCursor();
		}
		
		return $entities;
	}
}
