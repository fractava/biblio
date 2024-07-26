<?php

declare(strict_types=1);

namespace OCA\Biblio\Migration;

use Closure;
use OCP\DB\Types;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20210619161300 extends SimpleMigrationStep {
	// Collections
	public const COLLECTIONS_TABLE = "biblio_collections";
	public const COLLECTION_MEMBERS_TABLE = "biblio_collection_members";

	// Items
	public const ITEMS_TABLE = "biblio_items";
	public const ITEM_INSTANCES_TABLE = "biblio_item_instances";
	public const ITEM_FIELDS_TABLE = "biblio_item_fields";
	public const ITEM_FIELDS_VALUES_TABLE = "biblio_item_fields_values";

	public const LOANS_TABLE = "biblio_loans";
	public const LOAN_FIELDS_TABLE = "biblio_loan_fields";
	public const LOAN_FIELDS_VALUES_TABLE = "biblio_loan_fields_values";

	// Customers
	public const CUSTOMERS_TABLE = "biblio_customers";
	public const CUSTOMER_FIELDS_TABLE = "biblio_customer_fields";
	public const CUSTOMER_FIELDS_VALUES_TABLE = "biblio_customer_fields_values";

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
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);

			$table->addColumn('nomenclature_item', Types::STRING, [
				'notnull' => true,
				'length' => 100,
				'default' => 'ITEM',
			]);

			$table->addColumn('nomenclature_instance', Types::STRING, [
				'notnull' => true,
				'length' => 100,
				'default' => 'INSTANCE',
			]);

			$table->addColumn('nomenclature_customer', Types::STRING, [
				'notnull' => true,
				'length' => 100,
				'default' => 'CUSTOMER',
			]);

			$table->addColumn('item_fields_order', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('loan_fields_order', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('customer_fields_order', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);

			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable(self::COLLECTION_MEMBERS_TABLE)) {
			$table = $schema->createTable(self::COLLECTION_MEMBERS_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);

			// TODO: Permissions

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'members_collection_id_index');
			$table->addIndex(['user_id'], 'members_user_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'members_collection_id_fk');
			$table->addUniqueConstraint(['collection_id', 'user_id'], "members_collection_id_user_id_unique");
		}

		if (!$schema->hasTable(self::ITEMS_TABLE)) {
			$table = $schema->createTable(self::ITEMS_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('title', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'items_collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'items_collection_id_fk');
		}

		if (!$schema->hasTable(self::ITEM_FIELDS_TABLE)) {
			$table = $schema->createTable(self::ITEM_FIELDS_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('type', Types::STRING, [
				'notnull' => true,
				'length' => 50,
			]);
			$table->addColumn('settings', Types::STRING, [
				'notnull' => true,
				'length' => 5000,
			]);
			$table->addColumn('include_in_list', Types::BOOLEAN, [
				'notnull' => true,
			]);
			

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'item_fields_collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'item_fields_collection_id_fk');
			$table->addUniqueConstraint(['collection_id', 'name'], "item_fields_collection_id_name_unique");
		}

		if (!$schema->hasTable(self::ITEM_FIELDS_VALUES_TABLE)) {
			$table = $schema->createTable(self::ITEM_FIELDS_VALUES_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('item_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('field_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('value', Types::TEXT, [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['item_id'], 'item_fields_values_item_id_index');
			$table->addIndex(['field_id'], 'item_fields_values_field_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::ITEMS_TABLE),
				['item_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'item_fields_values_item_id_fk');

			$table->addForeignKeyConstraint(
				$schema->getTable(self::ITEM_FIELDS_TABLE),
				['field_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'item_fields_values_field_id_fk');
			$table->addUniqueConstraint(['item_id', 'field_id'], "item_fields_values_item_id_field_id_unique");
		}

		if (!$schema->hasTable(self::CUSTOMERS_TABLE)) {
			$table = $schema->createTable(self::CUSTOMERS_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 100,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'customers_collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'customers_collection_id_fk');
		}

		if (!$schema->hasTable(self::CUSTOMER_FIELDS_TABLE)) {
			$table = $schema->createTable(self::CUSTOMER_FIELDS_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('type', Types::STRING, [
				'notnull' => true,
				'length' => 50,
			]);
			$table->addColumn('settings', Types::STRING, [
				'notnull' => true,
				'length' => 5000,
			]);
			$table->addColumn('include_in_list', Types::BOOLEAN, [
				'notnull' => true,
			]);
			

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'customer_fields_collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'customer_fields_collection_id_fk');
			$table->addUniqueConstraint(['collection_id', 'name'], "customer_fields_collection_id_name_unique");
		}

		if (!$schema->hasTable(self::CUSTOMER_FIELDS_VALUES_TABLE)) {
			$table = $schema->createTable(self::CUSTOMER_FIELDS_VALUES_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('customer_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('field_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('value', Types::TEXT, [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['customer_id'], 'customer_fields_values_customer_id_index');
			$table->addIndex(['field_id'], 'customer_fields_values_field_id_index');
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
			$table->addUniqueConstraint(['customer_id', 'field_id'], "customer_fields_values_customer_id_field_id_unique");
		}

		if (!$schema->hasTable(self::ITEM_INSTANCES_TABLE)) {
			$table = $schema->createTable(self::ITEM_INSTANCES_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('barcode', Types::STRING, [
				'notnull' => true,
				'length' => 100,
			]);
			$table->addColumn('item_id', Types::INTEGER, [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['barcode'], 'barcodeIndex');
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

			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('item_instance_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('customer_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('until', Types::BIGINT, [
				'notnull' => false,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['item_instance_id'], 'loans_item_instance_id_index');
			$table->addUniqueConstraint(['item_instance_id'], "loans_item_instance_id_unique");
			$table->addForeignKeyConstraint(
				$schema->getTable(self::ITEM_INSTANCES_TABLE),
				['item_instance_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'loans_item_instance_id_fk');
			$table->addIndex(['customer_id'], 'loans_customer_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::CUSTOMERS_TABLE),
				['customer_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'loans_customer_id_fk');
		}

		if (!$schema->hasTable(self::LOAN_FIELDS_TABLE)) {
			$table = $schema->createTable(self::LOAN_FIELDS_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('collection_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('type', Types::STRING, [
				'notnull' => true,
				'length' => 50,
			]);
			$table->addColumn('settings', Types::STRING, [
				'notnull' => true,
				'length' => 5000,
			]);
			$table->addColumn('include_in_list', Types::BOOLEAN, [
				'notnull' => true,
			]);
			

			$table->setPrimaryKey(['id']);
			$table->addIndex(['collection_id'], 'loan_fields_collection_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::COLLECTIONS_TABLE),
				['collection_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'loan_fields_collection_id_fk');
			$table->addUniqueConstraint(['collection_id', 'name'], "loan_fields_collection_id_name_unique");
		}

		if (!$schema->hasTable(self::LOAN_FIELDS_VALUES_TABLE)) {
			$table = $schema->createTable(self::LOAN_FIELDS_VALUES_TABLE);
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('loan_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('field_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('value', Types::TEXT, [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['loan_id'], 'loan_fields_values_loan_id_index');
			$table->addIndex(['field_id'], 'loan_fields_values_field_id_index');
			$table->addForeignKeyConstraint(
				$schema->getTable(self::LOANS_TABLE),
				['loan_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'loan_fields_values_loan_id_fk');

			$table->addForeignKeyConstraint(
				$schema->getTable(self::LOAN_FIELDS_TABLE),
				['field_id'],
				['id'],
				['onDelete' => 'CASCADE'],
				'loan_fields_values_field_id_fk');
			$table->addUniqueConstraint(['loan_id', 'field_id'], "loan_fields_values_loan_id_field_id_unique");
		}

		return $schema;
	}
}
