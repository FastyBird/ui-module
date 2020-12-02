<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'create'          => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources',
		file_get_contents(__DIR__ . '/requests/dataSources.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/dataSources.create.json',
	],
	'missingRequired' => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources',
		file_get_contents(__DIR__ . '/requests/dataSources.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/dataSources.create.missing.required.json',
	],
	'widgetNotFound'  => [
		'/v1/widgets/11553443-4564-454d-af04-0dfeef08aa96/data-sources',
		file_get_contents(__DIR__ . '/requests/dataSources.create.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
	'invalidType'     => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources',
		file_get_contents(__DIR__ . '/requests/dataSources.create.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/dataSources.create.invalidType.json',
	],
];
