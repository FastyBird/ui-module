<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readOne'                  => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/display',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/display.read.json',
	],
	'readOneUnknownWidget'     => [
		'/v1/widgets/11553443-4564-454d-af04-0dfeef08aa96/display',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
	'readRelationshipsWidget'  => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/display/relationships/widget',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/display.readRelationships.widget.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/display/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
