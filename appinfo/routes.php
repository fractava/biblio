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

		// Mediums
		['name' => 'medium#index', 'url' => '/collections/{collectionId}/mediums', 'verb' => 'GET'],
        ['name' => 'medium#show', 'url' => '/collections/{collectionId}/mediums/{id}', 'verb' => 'GET'],
        ['name' => 'medium#create', 'url' => '/collections/{collectionId}/mediums', 'verb' => 'POST'],
        ['name' => 'medium#update', 'url' => '/collections/{collectionId}/mediums/{id}', 'verb' => 'PUT'],
        ['name' => 'medium#destroy', 'url' => '/collections/{collectionId}/mediums/{id}', 'verb' => 'DELETE'],

		// Medium Fields
		['name' => 'medium_field#index', 'url' => '/collections/{collectionId}/medium_fields', 'verb' => 'GET'],
        ['name' => 'medium_field#show', 'url' => '/collections/{collectionId}/medium_fields/{id}', 'verb' => 'GET'],
        ['name' => 'medium_field#create', 'url' => '/collections/{collectionId}/medium_fields', 'verb' => 'POST'],
        ['name' => 'medium_field#update', 'url' => '/collections/{collectionId}/medium_fields/{id}', 'verb' => 'PUT'],
        ['name' => 'medium_field#destroy', 'url' => '/collections/{collectionId}/medium_fields/{id}', 'verb' => 'DELETE'],
		['name' => 'medium_field#uniqueTitles', 'url' => '/collections/{collectionId}/medium_fields/uniqueTitles', 'verb' => 'GET'],
	]
];
