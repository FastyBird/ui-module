<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll'                  => [
		'/v1/dashboards',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dashboards.index.json',
	],
	'readAllPaging'            => [
		'/v1/dashboards?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dashboards.index.paging.json',
	],
	'readOne'                  => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dashboards.read.json',
	],
	'readOneUnknown'           => [
		'/v1/dashboards/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dashboards.notFound.json',
	],
	'readRelationshipsGroups'  => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c/relationships/groups',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dashboards.readRelationships.groups.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
