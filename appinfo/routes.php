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
		['name' => 'collection_members#index', 'url' => '/collections/{collectionId}/members', 'verb' => 'GET'],
		['name' => 'collection_members#show', 'url' => '/collections/{collectionId}/members/{id}', 'verb' => 'GET'],
		['name' => 'collection_members#create', 'url' => '/collections/{collectionId}/members', 'verb' => 'POST'],
		['name' => 'collection_members#update', 'url' => '/collections/{collectionId}/members/{id}', 'verb' => 'PUT'],
		['name' => 'collection_members#destroy', 'url' => '/collections/{collectionId}/members/{id}', 'verb' => 'DELETE'],

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
		['name' => 'item_field#uniqueTitles', 'url' => '/collections/{collectionId}/item_fields/uniqueTitles', 'verb' => 'GET'],
	]
];
