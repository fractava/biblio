<?php

declare(strict_types=1);

namespace OCA\Biblio\Migration;

use Closure;
use OCP\DB\Types;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20231116120500 extends SimpleMigrationStep {
	public const COLLECTIONS_TABLE = "biblio_collections";
	public const LOAN_UNTIL_PRESETS_TABLE = "biblio_loan_until_presets";

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		// LOAN UNTIL PRESETS
		if (!$schema->hasTable(self::LOAN_UNTIL_PRESETS_TABLE)) {
			$table = $schema->createTable(self::LOAN_UNTIL_PRESETS_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('type', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('timestamp', Types::BIGINT, [
				'notnull' => true,
			]);
			
			$table->setPrimaryKey(['id']);
			$table->addIndex(['type'], 'loan_until_presets_type_index');
			$table->addIndex(['collection_id'], 'loan_until_presets_collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'loan_until_presets_collection_id_fk');
		}

		return $schema;
	}
}
