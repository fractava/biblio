<?php

return [
	'resources' => [
		'medium' => ['url' => '/mediums'],
		'medium_api' => ['url' => '/api/0.1/mediums']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'medium_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']]
	]
];
