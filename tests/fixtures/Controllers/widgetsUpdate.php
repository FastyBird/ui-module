<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update'      => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96?include=display,data-sources',
		file_get_contents(__DIR__ . '/requests/widgets.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/widgets.update.json',
	],
	'invalidType' => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96?include=display,data-sources',
		file_get_contents(__DIR__ . '/requests/widgets.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/widgets.update.invalidType.json',
	],
	'idMismatch'  => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96?include=display,data-sources',
		file_get_contents(__DIR__ . '/requests/widgets.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
	'notFound'    => [
		'/v1/widgets/55553443-4564-454d-af04-0dfeef08aa96?include=display,data-sources',
		file_get_contents(__DIR__ . '/requests/widgets.update.notFound.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
];
