<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'create'          => [
		'/v1/dashboards',
		file_get_contents(__DIR__ . '/requests/dashboards.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/dashboards.create.json',
	],
	'missingRequired' => [
		'/v1/dashboards',
		file_get_contents(__DIR__ . '/requests/dashboards.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/missing.required.json',
	],
	'invalidType'     => [
		'/v1/dashboards',
		file_get_contents(__DIR__ . '/requests/dashboards.create.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/dashboards.create.invalidType.json',
	],
];
