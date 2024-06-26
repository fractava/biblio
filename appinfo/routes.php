<?php

return [
	'resources' => [
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

		// Collections
		['name' => 'collection#index', 'url' => '/collections', 'verb' => 'GET'],
		['name' => 'collection#show', 'url' => '/collections/{collectionId}', 'verb' => 'GET'],
		['name' => 'collection#create', 'url' => '/collections', 'verb' => 'POST'],
		['name' => 'collection#update', 'url' => '/collections/{collectionId}', 'verb' => 'PUT'],
		['name' => 'collection#destroy', 'url' => '/collections/{collectionId}', 'verb' => 'DELETE'],

		// Collection Members
		['name' => 'collection_member#index', 'url' => '/collections/{collectionId}/members', 'verb' => 'GET'],
		['name' => 'collection_member#show', 'url' => '/collections/{collectionId}/members/{id}', 'verb' => 'GET'],
		['name' => 'collection_member#create', 'url' => '/collections/{collectionId}/members', 'verb' => 'POST'],
		['name' => 'collection_member#update', 'url' => '/collections/{collectionId}/members/{id}', 'verb' => 'PUT'],
		['name' => 'collection_member#destroy', 'url' => '/collections/{collectionId}/members/{id}', 'verb' => 'DELETE'],

		// Items
		['name' => 'item#index', 'url' => '/collections/{collectionId}/items', 'verb' => 'GET'],
		['name' => 'item#show', 'url' => '/collections/{collectionId}/items/{id}', 'verb' => 'GET'],
		['name' => 'item#create', 'url' => '/collections/{collectionId}/items', 'verb' => 'POST'],
		['name' => 'item#update', 'url' => '/collections/{collectionId}/items/{id}', 'verb' => 'PUT'],
		['name' => 'item#destroy', 'url' => '/collections/{collectionId}/items/{id}', 'verb' => 'DELETE'],

		// Item Fields
		['name' => 'item_field#index', 'url' => '/collections/{collectionId}/item_fields', 'verb' => 'GET'],
		['name' => 'item_field#show', 'url' => '/collections/{collectionId}/item_fields/{id}', 'verb' => 'GET'],
		['name' => 'item_field#create', 'url' => '/collections/{collectionId}/item_fields', 'verb' => 'POST'],
		['name' => 'item_field#update', 'url' => '/collections/{collectionId}/item_fields/{id}', 'verb' => 'PUT'],
		['name' => 'item_field#destroy', 'url' => '/collections/{collectionId}/item_fields/{id}', 'verb' => 'DELETE'],

		// Item Field Values
		['name' => 'item_field_value#index', 'url' => '/collections/{collectionId}/items/{itemId}/field_values', 'verb' => 'GET'],
		['name' => 'item_field_value#show', 'url' => '/collections/{collectionId}/items/{itemId}/field_values/{fieldId}', 'verb' => 'GET'],
		['name' => 'item_field_value#update', 'url' => '/collections/{collectionId}/items/{itemId}/field_values/{fieldId}', 'verb' => 'PUT'],
		['name' => 'item_field_value#destroy', 'url' => '/collections/{collectionId}/items/{itemId}/field_values/{fieldId}', 'verb' => 'DELETE'],

		// Items Instances
		['name' => 'item_instance#index', 'url' => '/collections/{collectionId}/itemInstances', 'verb' => 'GET'],
		['name' => 'item_instance#show', 'url' => '/collections/{collectionId}/itemInstances/{instanceId}', 'verb' => 'GET'],
		['name' => 'item_instance#create', 'url' => '/collections/{collectionId}/itemInstances', 'verb' => 'POST'],
		['name' => 'item_instance#batchCreateTest', 'url' => '/collections/{collectionId}/itemInstances/batchTest', 'verb' => 'POST'],
		['name' => 'item_instance#batchCreate', 'url' => '/collections/{collectionId}/itemInstances/batch', 'verb' => 'POST'],
		['name' => 'item_instance#destroy', 'url' => '/collections/{collectionId}/itemInstances/{instanceId}', 'verb' => 'DELETE'],

		// Item Instances by Barcode
		['name' => 'item_instance#showByBarcode', 'url' => '/collections/{collectionId}/itemInstances/byBarcode/{barcode}', 'verb' => 'GET'],
		['name' => 'item_instance#destroyByBarcode', 'url' => '/collections/{collectionId}/itemInstances/byBarcode/{barcode}', 'verb' => 'DELETE'],

		['name' => 'item_instance#barcodePrefix', 'url' => '/collections/{collectionId}/itemInstances/barcodePrefix/{itemId}', 'verb' => 'GET'],

		// Loans
		['name' => 'loan#index', 'url' => '/collections/{collectionId}/loans', 'verb' => 'GET'],
		['name' => 'loan#show', 'url' => '/collections/{collectionId}/loans/byBarcode/{barcode}', 'verb' => 'GET'],
		['name' => 'loan#create', 'url' => '/collections/{collectionId}/loans', 'verb' => 'POST'],
		['name' => 'loan#update', 'url' => '/collections/{collectionId}/loans/byBarcode/{barcode}', 'verb' => 'PUT'],
		['name' => 'loan#destroy', 'url' => '/collections/{collectionId}/loans/byBarcode/{barcode}', 'verb' => 'DELETE'],

		// Loan Until Presets
		['name' => 'loan_until_preset#index', 'url' => '/collections/{collectionId}/loan_until_presets', 'verb' => 'GET'],
		['name' => 'loan_until_preset#show', 'url' => '/collections/{collectionId}/loan_until_presets/{id}', 'verb' => 'GET'],
		['name' => 'loan_until_preset#create', 'url' => '/collections/{collectionId}/loan_until_presets', 'verb' => 'POST'],
		['name' => 'loan_until_preset#update', 'url' => '/collections/{collectionId}/loan_until_presets/{id}', 'verb' => 'PUT'],
		['name' => 'loan_until_preset#destroy', 'url' => '/collections/{collectionId}/loan_until_presets/{id}', 'verb' => 'DELETE'],

		// Loan Fields
		['name' => 'loan_field#index', 'url' => '/collections/{collectionId}/loan_fields', 'verb' => 'GET'],
		['name' => 'loan_field#show', 'url' => '/collections/{collectionId}/loan_fields/{id}', 'verb' => 'GET'],
		['name' => 'loan_field#create', 'url' => '/collections/{collectionId}/loan_fields', 'verb' => 'POST'],
		['name' => 'loan_field#update', 'url' => '/collections/{collectionId}/loan_fields/{id}', 'verb' => 'PUT'],
		['name' => 'loan_field#destroy', 'url' => '/collections/{collectionId}/loan_fields/{id}', 'verb' => 'DELETE'],
		
		// Loan Field Values
		['name' => 'loan_field_value#index', 'url' => '/collections/{collectionId}/loans/{loanId}/field_values', 'verb' => 'GET'],
		['name' => 'loan_field_value#show', 'url' => '/collections/{collectionId}/loans/{loanId}/field_values/{fieldId}', 'verb' => 'GET'],
		['name' => 'loan_field_value#update', 'url' => '/collections/{collectionId}/loans/{loanId}/field_values/{fieldId}', 'verb' => 'PUT'],
		['name' => 'loan_field_value#destroy', 'url' => '/collections/{collectionId}/loans/{loanId}/field_values/{fieldId}', 'verb' => 'DELETE'],

		// Customers
		['name' => 'customer#index', 'url' => '/collections/{collectionId}/customers', 'verb' => 'GET'],
		['name' => 'customer#show', 'url' => '/collections/{collectionId}/customers/{id}', 'verb' => 'GET'],
		['name' => 'customer#create', 'url' => '/collections/{collectionId}/customers', 'verb' => 'POST'],
		['name' => 'customer#update', 'url' => '/collections/{collectionId}/customers/{id}', 'verb' => 'PUT'],
		['name' => 'customer#destroy', 'url' => '/collections/{collectionId}/customers/{id}', 'verb' => 'DELETE'],

		// Customer Fields
		['name' => 'customer_field#index', 'url' => '/collections/{collectionId}/customer_fields', 'verb' => 'GET'],
		['name' => 'customer_field#show', 'url' => '/collections/{collectionId}/customer_fields/{id}', 'verb' => 'GET'],
		['name' => 'customer_field#create', 'url' => '/collections/{collectionId}/customer_fields', 'verb' => 'POST'],
		['name' => 'customer_field#update', 'url' => '/collections/{collectionId}/customer_fields/{id}', 'verb' => 'PUT'],
		['name' => 'customer_field#destroy', 'url' => '/collections/{collectionId}/customer_fields/{id}', 'verb' => 'DELETE'],

		// Customer Field Values
		['name' => 'customer_field_value#index', 'url' => '/collections/{collectionId}/customers/{customerId}/field_values', 'verb' => 'GET'],
		['name' => 'customer_field_value#show', 'url' => '/collections/{collectionId}/customers/{customerId}/field_values/{fieldId}', 'verb' => 'GET'],
		['name' => 'customer_field_value#update', 'url' => '/collections/{collectionId}/customers/{customerId}/field_values/{fieldId}', 'verb' => 'PUT'],
		['name' => 'customer_field_value#destroy', 'url' => '/collections/{collectionId}/customers/{customerId}/field_values/{fieldId}', 'verb' => 'DELETE'],

		// Imports
		['name' => 'v1_import#import', 'url' => '/import/v1', 'verb' => 'POST'],

		// History
		['name' => 'history_entry#index', 'url' => '/collections/{collectionId}/history', 'verb' => 'GET'],
		['name' => 'history_entry#show', 'url' => '/collections/{collectionId}/history/{id}', 'verb' => 'GET'],
		['name' => 'history_entry#destroy', 'url' => '/collections/{collectionId}/history/{id}', 'verb' => 'DELETE'],
	]
];
