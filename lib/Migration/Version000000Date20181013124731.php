<?php

declare(strict_types=1);

namespace OCA\Biblio\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20181013124731 extends SimpleMigrationStep {

    const LIBRARIES_TABLE = "biblio_libraries";
    const MEDIUMS_TABLE = "biblio_mediums";
	const MEDIUM_FIELDS_TABLE = "biblio_medium_fields";

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable(self::LIBRARIES_TABLE)) {
			$table = $schema->createTable(self::LIBRARIES_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('name', 'string', [
				'notnull' => true,
				'length' => 200,
			]);

			$table->setPrimaryKey(['id']);
		}

		if(!§schema->hasTable('biblio_library_members')) {
			$table = $schema->createTable('biblio_library_members');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('library_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 200,
			]);

			// TODO: Permissions

			$table->setPrimaryKey(['id']);
			$table->addIndex(['library_id'], 'library_id_index');
			$table->addForeignKeyConstraint($schema->getTable(self::LIBRARIES_TABLE), ['library_id'], ['id'], ['onDelete' => 'CASCADE'], 'members_library_id_fk');
		}

		if (!$schema->hasTable(self::MEDIUMS_TABLE)) {
			$table = $schema->createTable(self::MEDIUMS_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('library_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('title', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('fields_order', 'string', [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['library_id'], 'library_id_index');
			$table->addForeignKeyConstraint($schema->getTable(self::LIBRARIES_TABLE), ['library_id'], ['id'], ['onDelete' => 'CASCADE'], 'mediums_library_id_fk');
		}

		if (!$schema->hasTable(self::MEDIUM_FIELDS_TABLE)) {
			$table = $schema->createTable(self::MEDIUM_FIELDS_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('medium_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('type', 'string', [
				'notnull' => true,
				'length' => 50,
			]);
			$table->addColumn('title', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('value', 'text', [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['medium_id'], 'mediumIdIndex');
			$table->addForeignKeyConstraint($schema->getTable(self::MEDIUMS_TABLE), ['medium_id'], ['id'], ['onDelete' => 'CASCADE'], 'medium_fields_medium_id_fk');
		}
		return $schema;
	}
}
