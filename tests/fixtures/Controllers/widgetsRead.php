<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll'                      => [
		'/v1/widgets',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/widgets.index.json',
	],
	'readAllPaging'                => [
		'/v1/widgets?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/widgets.index.paging.json',
	],
	'readOne'                      => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/widgets.read.json',
	],
	'readOneWithInclude'           => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96?include=display,data-sources',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/widgets.read.include.json',
	],
	'readOneUnknown'               => [
		'/v1/widgets/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
	'readRelationshipsDisplay'     => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/relationships/display',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/widgets.readRelationships.display.json',
	],
	'readRelationshipsDataSources' => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/relationships/data-sources',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/widgets.readRelationships.dataSources.json',
	],
	'readRelationshipsGroups'      => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/relationships/groups',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/widgets.readRelationships.groups.json',
	],
	'readRelationshipsUnknown'     => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
