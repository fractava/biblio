<?php

namespace OCA\Biblio\Traits;

use OCP\DB\QueryBuilder\IQueryBuilder;

trait ApiObjectMapper {

    /**
     * @param IQueryBuilder $qb
     * @param array $filter
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
}