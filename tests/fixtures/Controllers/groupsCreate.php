<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'create'            => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c/groups',
		file_get_contents(__DIR__ . '/requests/groups.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/groups.create.json',
	],
	'missingRequired'   => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c/groups',
		file_get_contents(__DIR__ . '/requests/groups.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/missing.required.json',
	],
	'dashboardNotFound' => [
		'/v1/dashboards/aa2379d8-8351-44b6-ad8d-73a0abcb7f9c/groups',
		file_get_contents(__DIR__ . '/requests/groups.create.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dashboards.notFound.json',
	],
	'invalidType'       => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c/groups',
		file_get_contents(__DIR__ . '/requests/groups.create.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/groups.create.invalidType.json',
	],
];
