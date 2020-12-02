<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'delete'            => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c',
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/groups.delete.json',
	],
	'deleteUnknown'     => [
		'/v1/dashboards/ab369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/88f4a14f-7f78-4216-99b8-584ab9229f1c',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/groups.notFound.json',
	],
	'dashboardNotFound' => [
		'/v1/dashboards/aa369e71-ada6-4d1a-a5a8-b6ee5cd58296/groups/89f4a14f-7f78-4216-99b8-584ab9229f1c',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dashboards.notFound.json',
	],
];
