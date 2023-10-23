<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\Biblio\Traits\ApiObjectMapper;

class LoanFieldValueMapper extends QBMapper {
	use ApiObjectMapper;

	const TABLENAME = 'biblio_loan_fields_values';

	const FIELDS_TABLENAME = 'biblio_loan_fields';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, LoanFieldValue::class);
	}

	/**
	 * @param array $parameters
	 * @return Entity|LoanFieldValue
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(array $parameters): LoanFieldValue {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$findById = array_key_exists("id", $parameters);
		$findByLoanIdFieldId = array_key_exists("loanId", $parameters) && array_key_exists("fieldId", $parameters);

		$qb->select('*')
			->from(self::TABLENAME);

		if ($findById) {
			$qb->where($qb->expr()->eq('id', $qb->createNamedParameter($parameters["id"], IQueryBuilder::PARAM_INT)));
		} else if ($findByLoanIdFieldId) {
			$qb->where($qb->expr()->eq('loan_id', $qb->createNamedParameter($parameters["loanId"], IQueryBuilder::PARAM_INT)))
            	->andWhere($qb->expr()->eq('field_id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)));
		} else {
			throw new Exception("Invalid parameters supplied to LoanFieldValueMapper->find");
		}

		return $this->findEntity($qb);
	}

	/**
	 * @param array $parameters
	 * @return LoanFieldValueFieldCombination
	 */
	public function findIncludingField(array $parameters): LoanFieldValueFieldCombination {
		if (array_key_exists("collectionId", $parameters)) {
			/* @var $qb IQueryBuilder */
			$qb = $this->db->getQueryBuilder();

			$qb->select('*')
				->addSelect("f.id AS field_id")
				->from(self::TABLENAME, 'v');

			$findById = array_key_exists("id", $parameters);
			$findByLoanIdFieldId = array_key_exists("loanId", $parameters) && array_key_exists("fieldId", $parameters);

			if ($findById) {
				$joinON = [
					$qb->expr()->eq('v.id', $qb->createNamedParameter($parameters["id"], IQueryBuilder::PARAM_INT)),
					$qb->expr()->eq('v.field_id', 'f.id'),
				];
			} else if ($findByLoanIdFieldId) {
				$joinON = [
					$qb->expr()->eq('v.loan_id', $qb->createNamedParameter($parameters["loanId"], IQueryBuilder::PARAM_INT)),
					$qb->expr()->eq('v.field_id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)),
				];
			} else {
				throw new Exception("Invalid parameters supplied to LoanFieldValueMapper->findIncludingField");
			}
			
			$qb->rightJoin('v', self::FIELDS_TABLENAME, 'f', $qb->expr()->andX(...$joinON));

			$collectionExpr = $qb->expr()->eq('f.collection_id', $qb->createNamedParameter($parameters["collectionId"], IQueryBuilder::PARAM_INT));

			if ($findById) {
				$qb->where($collectionExpr);
			} else if ($findByLoanIdFieldId) {
				$qb->where($qb->expr()->eq('f.id', $qb->createNamedParameter($parameters["fieldId"], IQueryBuilder::PARAM_INT)));
				$qb->andWhere($collectionExpr);
			}

			$result = LoanFieldValueFieldCombination::fromRow($this->findOneQuery($qb));
			
			return $result;
		} else {
			throw new Exception("Invalid parameters supplied to LoanFieldValueMapper->findIncludingField");
		}
	}

	/**
	 * @param int $loanId
	 * @return array
	 */
	public function findAll(int $loanId, ?array $filters): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from(self::TABLENAME)
			->where($qb->expr()->eq('loan_id', $qb->createNamedParameter($loanId, IQueryBuilder::PARAM_INT)));
		
		return $this->findEntities($qb);
	}

    /**
	 * @param int $loanId
	 * @return array
	 */
	public function findAllIncludingFields(int $collectionId, int $loanId, ?array $filters, ?int $limit = null, ?int $offset = null): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
        $qb->select('*')
			->addSelect("f.id AS field_id")
            ->from(self::TABLENAME, 'v')
            ->rightJoin('v', self::FIELDS_TABLENAME, 'f', $qb->expr()->andX(
                $qb->expr()->eq('v.field_id', 'f.id'),
                $qb->expr()->eq('v.loan_id', $qb->createNamedParameter($loanId, IQueryBuilder::PARAM_INT))
            ))
			->where($qb->expr()->eq('f.collection_id', $qb->createNamedParameter($collectionId, IQueryBuilder::PARAM_INT)));

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
				$entities[] = LoanFieldValueFieldCombination::fromRow($row);
			}
			return $entities;
		} finally {
			$result->closeCursor();
		}
        
		return $entities;
	}
}
