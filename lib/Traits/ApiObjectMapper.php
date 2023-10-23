<?php

namespace OCA\Biblio\Traits;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

trait ApiObjectMapper {

    /**
     * @param IQueryBuilder $qb
     * @param array $filter
     * @param string $column
     * @param bool $and
     */
    public function handleBooleanFilter(IQueryBuilder $qb, ?array $filter, string $column, bool $and = true) {
        if(isset($filter) && is_bool($filter["operand"])) {
			if($filter["operator"] === "=") {
				$expression = $qb->expr()->eq($column, $qb->createNamedParameter($filter["operand"], IQueryBuilder::PARAM_BOOL));
			}

            if($and) {
                $qb->andWhere($expression);
            } else {
                $qb->where($expression);
            }
		}
    }

    public function handleJsonStringFilterExpr(IDBConnection $db, IQueryBuilder $qb, ?array $filter, string $column) {
        if(isset($filter) && is_string($filter["operand"]) && $filter["operand"] !== '') {
            if($filter["operator"] === "=") {
				return $qb->expr()->orX(
					$qb->expr()->eq($column, $qb->createNamedParameter("\"" . $filter["operand"] . "\"", IQueryBuilder::PARAM_STR)),
					$qb->expr()->eq($column, $qb->createNamedParameter($filter["operand"], IQueryBuilder::PARAM_STR))
				);
			} else if($filter["operator"] === "contains") {
                return $qb->expr()->iLike($column, $qb->createNamedParameter('%' . $db->escapeLikeParameter($filter["operand"]) . '%'));
            } else if($filter["operator"] === "startsWith") {
                return $qb->expr()->orX(
					$qb->expr()->iLike($column, $qb->createNamedParameter($db->escapeLikeParameter("\"" . $filter["operand"]) . '%')),
					$qb->expr()->iLike($column, $qb->createNamedParameter($db->escapeLikeParameter($filter["operand"]) . '%'))
				);
            } else if($filter["operator"] === "endsWith") {
                return $qb->expr()->orX(
					$qb->expr()->iLike($column, $qb->createNamedParameter('%' . $db->escapeLikeParameter($filter["operand"] . "\""))),
					$qb->expr()->iLike($column, $qb->createNamedParameter('%' . $db->escapeLikeParameter($filter["operand"])))
				);
            } else {
                throw new \Error("unknown operator");
            }
        }
    }

    public function handleJsonStringFilter(IDBConnection $db, IQueryBuilder $qb, ?array $filter, string $column, bool $and = true) {
        if(isset($filter) && is_string($filter["operand"]) && $filter["operand"] !== '') {
			$expression = $this->handleJsonStringFilterExpr($db, $qb, $filter, $column);

            if($and) {
                $qb->andWhere($expression);
            } else {
                $qb->where($expression);
            }
        }
    }

	public function handleStringFilter(IDBConnection $db, IQueryBuilder $qb, ?array $filter, string $column, bool $and = true) {
        if(isset($filter) && is_string($filter["operand"]) && $filter["operand"] !== '') {
			if($filter["operator"] === "=") {
				$expression = $qb->expr()->eq($column, $qb->createNamedParameter($filter["operand"], IQueryBuilder::PARAM_STR));
			} else if($filter["operator"] === "contains") {
                $expression = $qb->expr()->iLike($column, $qb->createNamedParameter('%' . $db->escapeLikeParameter($filter["operand"]) . '%'));
            } else if($filter["operator"] === "startsWith") {
                $expression = $qb->expr()->iLike($column, $qb->createNamedParameter($db->escapeLikeParameter($filter["operand"]) . '%'));
            } else if($filter["operator"] === "endsWith") {
                $expression = $qb->expr()->iLike($column, $qb->createNamedParameter('%' . $db->escapeLikeParameter($filter["operand"])));
            } else {
                throw new \Error("unknown operator");
            }
            if($and) {
                $qb->andWhere($expression);
            } else {
                $qb->where($expression);
            }
        }
    }

	/**
     * @param IQueryBuilder $qb
     * @param array $filter
     * @param string $column
     * @param bool $and
     */
    public function handleIdFilter(IQueryBuilder $qb, ?array $filter, string $column, bool $and = true) {
        if(isset($filter) && is_int($filter["operand"])) {
			if($filter["operator"] === "=") {
				$expression = $qb->expr()->eq($column, $qb->createNamedParameter($filter["operand"], IQueryBuilder::PARAM_INT));
			}

            if($and) {
                $qb->andWhere($expression);
            } else {
                $qb->where($expression);
            }
		}
    }

	public function handleSortByColumn(IQueryBuilder $qb, string $column, bool $reverse) {
		$sortDirection = $this->getSortDirection($reverse);
		$qb->orderBy($column, $sortDirection);
	}

	public function handleOffset(IQueryBuilder $qb, int $offset) {
		if (isset($offset) && $offset > 0) {
			$qb->setFirstResult($offset);
		}
	}

	public function handleLimit(IQueryBuilder $qb, int $limit) {
		if (isset($limit) && $limit > 0) {
			$qb->setMaxResults($limit);
		}
	}

	public function isFieldReference(string $name) {
		if(str_starts_with($name, "field:")) {
			$fieldId = substr($name, strlen("field:"));
			if(ctype_digit($fieldId)) {
				return true;
			}
		}

		return false;
	}

	public function parseFieldReference(string $name) {
		$fieldId = substr($name, strlen("field:"));
		return ((int) $fieldId);
	}

	public function getFieldValueFilters(array $filters) {
		$fieldValueFilters = [];
		foreach ($filters as $key => $value) {
			if($this->isFieldReference($key)) {
				$filterFieldId = $this->parseFieldReference($key);
				$fieldValueFilters[$filterFieldId] = $value;
			}
		}

		return $fieldValueFilters;
	}

	public function getValidFieldValueCombinations(IDBConnection $db, IQueryBuilder $qb, array $fieldValueFilters, string $fieldIdColumn, string $valueColumn) {
		$validCombinations = [];
		foreach ($fieldValueFilters as $fieldId => $filter) {
			$validCombinations[] = $qb->expr()->andX(
				$qb->expr()->eq($fieldIdColumn, $qb->createNamedParameter($fieldId), IQueryBuilder::PARAM_INT),
				$this->handleJsonStringFilterExpr($this->db, $qb, $filter, $valueColumn),
			);
		}

		return $validCombinations;
	}

	public function getSortDirection(bool $reverse) {
		return $reverse ? "DESC" : "ASC";
	}

	public function sortByJoinedFieldValue(IQueryBuilder $qb, string $fieldReference, bool $reverse, string $leftTableAlias, string $fieldValueTableName, string $idJoinColumn) {
		$sortFieldId = $this->parseFieldReference($fieldReference);
		$sortDirection = $this->getSortDirection($reverse);

		$qb->leftJoin($leftTableAlias, $fieldValueTableName, 'sort', $qb->expr()->andX(
			$qb->expr()->eq($leftTableAlias . '.id', 'sort.' . $idJoinColumn),
			$qb->expr()->eq('sort.field_id', $qb->createNamedParameter($sortFieldId), IQueryBuilder::PARAM_INT),
		));

		$qb->orderBy('sort.value', $sortDirection);
	}
}