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

    public function handleStringFilterExpr(IDBConnection $db, IQueryBuilder $qb, ?array $filter, string $column) {
        if(isset($filter) && is_string($filter["operand"]) && $filter["operand"] !== '') {
            if($filter["operator"] === "=") {
				return $qb->expr()->eq($column, $qb->createNamedParameter("\"" . $filter["operand"] . "\"", IQueryBuilder::PARAM_STR));
			} else if($filter["operator"] === "contains") {
                return $qb->expr()->iLike($column, $qb->createNamedParameter('%' . $db->escapeLikeParameter($filter["operand"]) . '%'));
            } else if($filter["operator"] === "startsWith") {
                return $qb->expr()->iLike($column, $qb->createNamedParameter($db->escapeLikeParameter("\"" . $filter["operand"]) . '%'));
            } else if($filter["operator"] === "endsWith") {
                return $qb->expr()->iLike($column, $qb->createNamedParameter('%' . $db->escapeLikeParameter($filter["operand"] . "\"")));
            } else {
                throw new \Error("unknown operator");
            }
        }
    }

    public function handleStringFilter(IDBConnection $db, IQueryBuilder $qb, ?array $filter, string $column, bool $and = true) {
        if(isset($filter) && is_string($filter["operand"]) && $filter["operand"] !== '') {
			$expression = $this->handleStringFilterExpr($db, $qb, $filter, $column);

            if($and) {
                $qb->andWhere($expression);
            } else {
                $qb->where($expression);
            }
        }
    }
}