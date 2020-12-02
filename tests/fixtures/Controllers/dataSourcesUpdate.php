<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update'         => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377',
		file_get_contents(__DIR__ . '/requests/dataSources.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dataSources.update.json',
	],
	'invalidType'    => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377',
		file_get_contents(__DIR__ . '/requests/dataSources.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/dataSources.update.invalidType.json',
	],
	'idMismatch'     => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377',
		file_get_contents(__DIR__ . '/requests/dataSources.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
	'notFound'       => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/774937a7-8565-472e-8e12-fe97cd55a377',
		file_get_contents(__DIR__ . '/requests/dataSources.update.notFound.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dataSources.notFound.json',
	],
	'widgetNotFound' => [
		'/v1/widgets/11553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377',
		file_get_contents(__DIR__ . '/requests/dataSources.update.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
];
