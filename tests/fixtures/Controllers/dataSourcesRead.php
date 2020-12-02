<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll'                  => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dataSources.index.json',
	],
	'readAllPaging'            => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dataSources.index.paging.json',
	],
	'readOne'                  => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dataSources.read.json',
	],
	'readOneUnknown'           => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dataSources.notFound.json',
	],
	'readOneUnknownWidget'     => [
		'/v1/widgets/bb369e71-ada6-4d1a-a5a8-b6ee5cd58296/data-sources/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
	'readRelationshipsWidget'  => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377/relationships/widget',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dataSources.readRelationships.widget.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
