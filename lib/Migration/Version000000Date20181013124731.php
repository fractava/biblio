<?php

declare(strict_types=1);

namespace OCA\Biblio\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20181013124731 extends SimpleMigrationStep {

	// Collections
    const COLLECTIONS_TABLE = "biblio_collections";
	const COLLECTION_MEMBERS_TABLE = "biblio_collection_members";

	// Items
    const ITEMS_TABLE = "biblio_items";
	const ITEM_INSTANCES_TABLE = "biblio_item_instances";
	const ITEM_FIELDS_TABLE = "biblio_item_fields";
	const ITEM_FIELDS_VALUES_TABLE = "biblio_item_fields_values";

	// Customers
	const CUSTOMERS_TABLE = "biblio_customers";
	const CUSTOMER_FIELDS_TABLE = "biblio_customer_fields";
	const CUSTOMER_FIELDS_VALUES_TABLE = "biblio_customer_fields_values";

	const LOANS_TABLE = "biblio_loans";

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable(self::COLLECTIONS_TABLE)) {
			$table = $schema->createTable(self::COLLECTIONS_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('name', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('item_fields_order', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('customer_fields_order', 'string', [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
		}

		if(!$schema->hasTable(self::COLLECTION_MEMBERS_TABLE)) {
			$table = $schema->createTable(self::COLLECTION_MEMBERS_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 200,
			]);

			// TODO: Permissions

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'collection_id_index');
			$table->addIndex(['user_id'], 'user_id_index');
			$table->addForeignKeyConstraint($schema->getTable(self::COLLECTIONS_TABLE), ['collection_id'], ['id'], ['onDelete' => 'CASCADE'], 'members_collection_id_fk');
			$table->addUniqueConstraint(['collection_id', 'user_id'], "collection_id_user_id_unique");
		}

		if (!$schema->hasTable(self::ITEMS_TABLE)) {
			$table = $schema->createTable(self::ITEMS_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('title', 'string', [
				'notnull' => true,
				'length' => 200,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'items_collection_id_fk');
		}

		if (!$schema->hasTable(self::ITEM_FIELDS_TABLE)) {
			$table = $schema->createTable(self::ITEM_FIELDS_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('name', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('type', 'string', [
				'notnull' => true,
				'length' => 50,
			]);
			$table->addColumn('settings', 'string', [
				'notnull' => true,
				'length' => 5000,
			]);
			$table->addColumn('include_in_list', 'boolean', [
				'notnull' => true,
			]);
			

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'item_fields_collection_id_fk');
			$table->addUniqueConstraint(['collection_id', 'name'], "collection_id_name_unique");
		}

		if (!$schema->hasTable(self::ITEM_FIELDS_VALUES_TABLE)) {
			$table = $schema->createTable(self::ITEM_FIELDS_VALUES_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('item_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('field_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('value', 'text', [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['item_id'], 'itemIdIndex');
			$table->addIndex(['field_id'], 'fieldIdIndex');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::ITEMS_TABLE),
				['item_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'item_fields_item_id_fk');

			$table->addForeignKeyConstraint(
				$schema->getTable(self::ITEM_FIELDS_TABLE),
				['field_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'item_fields_field_id_fk');
			$table->addUniqueConstraint(['item_id', 'field_id'], "item_id_field_id_unique");
		}

		if (!$schema->hasTable(self::CUSTOMERS_TABLE)) {
			$table = $schema->createTable(self::CUSTOMERS_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('name', 'string', [
				'notnull' => true,
				'length' => 100,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'customers_collection_id_fk');
		}

		if (!$schema->hasTable(self::CUSTOMER_FIELDS_TABLE)) {
			$table = $schema->createTable(self::CUSTOMER_FIELDS_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('name', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('type', 'string', [
				'notnull' => true,
				'length' => 50,
			]);
			$table->addColumn('settings', 'string', [
				'notnull' => true,
				'length' => 5000,
			]);
			$table->addColumn('include_in_list', 'boolean', [
				'notnull' => true,
			]);
			

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'customer_fields_collection_id_fk');
			$table->addUniqueConstraint(['collection_id', 'name'], "collection_id_name_unique");
		}

		if (!$schema->hasTable(self::CUSTOMER_FIELDS_VALUES_TABLE)) {
			$table = $schema->createTable(self::CUSTOMER_FIELDS_VALUES_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('customer_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('field_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('value', 'text', [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['customer_id'], 'customerIdIndex');
			$table->addIndex(['field_id'], 'fieldIdIndex');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::CUSTOMERS_TABLE),
				['customer_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'customer_fields_values_customer_id_fk');

			$table->addForeignKeyConstraint(
				$schema->getTable(self::CUSTOMER_FIELDS_TABLE),
				['field_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'customer_fields_values_field_id_fk');
			$table->addUniqueConstraint(['customer_id', 'field_id'], "customer_id_field_id_unique");
		}

		if (!$schema->hasTable(self::ITEM_INSTANCES_TABLE)) {
			$table = $schema->createTable(self::ITEM_INSTANCES_TABLE);
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('barcode', 'string', [
				'notnull' => true,
				'length' => 100,
			]);
			$table->addColumn('item_id', 'integer', [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['barcode'], 'barcodeIndex');
			$table->addUniqueConstraint(['barcode'], "barcode_unique");
			$table->addIndex(['item_id'], 'itemIdIndex');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::ITEMS_TABLE),
				['item_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'item_id_fk');
		}

		if (!$schema->hasTable(self::LOANS_TABLE)) {
			$table = $schema->createTable(self::LOANS_TABLE);

			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('item_instance_id', 'integer', [
				'notnull' => false,
			]);
			$table->addColumn('customer_id', 'integer', [
				'notnull' => false,
			]);
			$table->addColumn('until', 'integer', [
				'notnull' => false,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['item_instance_id'], 'item_instance_id_index');
			$table->addUniqueConstraint(['item_instance_id'], "item_instance_id_unique");
			$table->addForeignKeyConstraint(
				$schema->getTable(self::ITEM_INSTANCES_TABLE),
				['item_instance_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'loaned_instance_id_fk');
			$table->addIndex(['customer_id'], 'customer_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::CUSTOMERS_TABLE),
				['customer_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'loaned_customer_id_fk');
		}

		return $schema;
	}
}
