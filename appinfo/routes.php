<?php

return [
	'resources' => [
		'medium' => ['url' => '/mediums'],
		'medium_api' => ['url' => '/api/0.1/mediums'],
		'medium_field' => ['url' => '/medium_fields']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'medium_field#uniqueTitles', 'url' => '/medium_fields/uniqueTitles', 'verb' => 'GET'],
		['name' => 'medium_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']]
	]
];
