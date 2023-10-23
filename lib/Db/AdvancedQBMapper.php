<?php

namespace OCA\Biblio\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class AdvancedQBMapper extends QBMapper {
	/**
	 * @param IDBConnection $db Instance of the Db abstraction layer
	 * @param string $tableName the name of the table. set this to allow entity
	 * @param class-string<T>|null $entityClass the name of the entity that the sql should be
	 * mapped to queries without using sql
	 * @since 14.0.0
	 */
	public function __construct(IDBConnection $db, string $tableName, string $entityClass = null) {
		parent::__construct($db, $tableName, $entityClass);
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
				$entities[] = $this->mapRowToEntity($filteredRow);
			}
			return [$entities, $seperateColumnResults];
		} finally {
			$result->closeCursor();
		}
	}
}
