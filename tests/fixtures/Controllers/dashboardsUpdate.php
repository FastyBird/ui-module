<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update'      => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c',
		file_get_contents(__DIR__ . '/requests/dashboards.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/dashboards.update.json',
	],
	'invalidType' => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c',
		file_get_contents(__DIR__ . '/requests/dashboards.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/dashboards.update.invalidType.json',
	],
	'idMismatch'  => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c',
		file_get_contents(__DIR__ . '/requests/dashboards.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
];
