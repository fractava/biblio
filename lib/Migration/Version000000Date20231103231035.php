<?php

declare(strict_types=1);

namespace OCA\Biblio\Migration;

use Closure;
use OCP\DB\Types;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20231103231035 extends SimpleMigrationStep {
	public const HISTORY_TABLE = "biblio_history_entries";

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		// HISTORY ENTRIES
		if (!$schema->hasTable(self::HISTORY_TABLE)) {
			$table = $schema->createTable(self::HISTORY_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('type', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('sub_entry_of', Types::INTEGER, [
				'notnull' => false,
			]);
			
			$table->addColumn('collection_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('collection_member_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('item_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('item_field_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('item_field_value_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('loan_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('loan_field_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('loan_field_value_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('customer_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('customer_field_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('customer_field_value_id', Types::INTEGER, [
				'notnull' => false,
			]);

			$table->addColumn('properties', Types::STRING, [
				'notnull' => true,
				'length' => 2000,
			]);
			
			$table->setPrimaryKey(['id']);
			$table->addIndex(['type'], 'type_index');
			$table->addIndex(['sub_entry_of'], 'sub_entry_of_index');
			$table->addIndex(['collection_id'], 'collection_id_index');
			// Foreign key to itself
			$table->addForeignKeyConstraint(
				$schema->getTable(self::HISTORY_TABLE),
				['sub_entry_of'],
				['id'],
				['onDelete' => 'CASCADE'],
				'history_entries_sub_entry_of_fk');
		}

		return $schema;
	}
}
