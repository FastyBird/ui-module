<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update'            => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c',
		file_get_contents(__DIR__ . '/requests/groups.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/groups.update.json',
	],
	'invalidType'       => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c',
		file_get_contents(__DIR__ . '/requests/groups.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/groups.update.invalidType.json',
	],
	'idMismatch'        => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c',
		file_get_contents(__DIR__ . '/requests/groups.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
	'notFound'          => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/88f4a14f-7f78-4216-99b8-584ab9229f1c',
		file_get_contents(__DIR__ . '/requests/groups.update.notFound.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/groups.notFound.json',
	],
	'dashboardNotFound' => [
		'/v1/dashboards/bb369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c',
		file_get_contents(__DIR__ . '/requests/groups.update.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dashboards.notFound.json',
	],
];
