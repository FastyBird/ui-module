<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'create'          => [
		'/v1/widgets',
		file_get_contents(__DIR__ . '/requests/widgets.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/widgets.create.json',
	],
	'missingRequired' => [
		'/v1/widgets',
		file_get_contents(__DIR__ . '/requests/widgets.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/missing.required.json',
	],
	'invalidType'     => [
		'/v1/widgets',
		file_get_contents(__DIR__ . '/requests/widgets.create.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/widgets.create.invalidType.json',
	],
	'invalidDisplay'  => [
		'/v1/widgets',
		file_get_contents(__DIR__ . '/requests/widgets.create.invalidDisplay.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/widgets.create.invalidDisplay.json',
	],
];
