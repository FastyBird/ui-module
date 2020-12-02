<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll'                    => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c/groups',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/groups.index.json',
	],
	'readAllPaging'              => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c/groups?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/groups.index.paging.json',
	],
	'readOne'                    => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/groups.read.json',
	],
	'readOneUnknown'             => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/groups.notFound.json',
	],
	'readOneUnknownDashboard'    => [
		'/v1/dashboards/bb369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dashboards.notFound.json',
	],
	'readRelationshipsWidgets'   => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c/relationships/widgets',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/groups.readRelationships.widgets.json',
	],
	'readRelationshipsDashboard' => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c/relationships/dashboard',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/groups.readRelationships.dashboard.json',
	],
	'readRelationshipsUnknown'   => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
