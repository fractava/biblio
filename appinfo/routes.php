<?php

return [
	'resources' => [
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

		// Libraries
		['name' => 'library#index', 'url' => '/libraries', 'verb' => 'GET'],
		['name' => 'library#show', 'url' => '/libraries/{libraryId}', 'verb' => 'GET'],
		['name' => 'library#create', 'url' => '/libraries', 'verb' => 'POST'],
        ['name' => 'library#update', 'url' => '/libraries/{libraryId}', 'verb' => 'PUT'],
        ['name' => 'library#destroy', 'url' => '/libraries/{libraryId}', 'verb' => 'DELETE'],

		// Library Members
		['name' => 'library_members#index', 'url' => '/libraries/{libraryId}/members', 'verb' => 'GET'],
		['name' => 'library_members#show', 'url' => '/libraries/{libraryId}/members/{id}', 'verb' => 'GET'],
		['name' => 'library_members#create', 'url' => '/libraries/{libraryId}/members', 'verb' => 'POST'],
		['name' => 'library_members#update', 'url' => '/libraries/{libraryId}/members/{id}', 'verb' => 'PUT'],
		['name' => 'library_members#destroy', 'url' => '/libraries/{libraryId}/members/{id}', 'verb' => 'DELETE'],

		// Mediums
		['name' => 'medium#index', 'url' => '/libraries/{libraryId}/mediums', 'verb' => 'GET'],
        ['name' => 'medium#show', 'url' => '/libraries/{libraryId}/mediums/{id}', 'verb' => 'GET'],
        ['name' => 'medium#create', 'url' => '/libraries/{libraryId}/mediums', 'verb' => 'POST'],
        ['name' => 'medium#update', 'url' => '/libraries/{libraryId}/mediums/{id}', 'verb' => 'PUT'],
        ['name' => 'medium#destroy', 'url' => '/libraries/{libraryId}/mediums/{id}', 'verb' => 'DELETE'],

		// Medium Fields
		['name' => 'medium_field#index', 'url' => '/libraries/{libraryId}/medium_fields', 'verb' => 'GET'],
        ['name' => 'medium_field#show', 'url' => '/libraries/{libraryId}/medium_fields/{id}', 'verb' => 'GET'],
        ['name' => 'medium_field#create', 'url' => '/libraries/{libraryId}/medium_fields', 'verb' => 'POST'],
        ['name' => 'medium_field#update', 'url' => '/libraries/{libraryId}/medium_fields/{id}', 'verb' => 'PUT'],
        ['name' => 'medium_field#destroy', 'url' => '/libraries/{libraryId}/medium_fields/{id}', 'verb' => 'DELETE'],
		['name' => 'medium_field#uniqueTitles', 'url' => '/libraries/{libraryId}/medium_fields/uniqueTitles', 'verb' => 'GET'],
	]
];
