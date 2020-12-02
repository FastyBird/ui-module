<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update'         => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/display',
		file_get_contents(__DIR__ . '/requests/display.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/display.update.json',
	],
	'invalidType'    => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/display',
		file_get_contents(__DIR__ . '/requests/display.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/display.update.invalidType.json',
	],
	'widgetNotFound' => [
		'/v1/widgets/11553443-4564-454d-af04-0dfeef08aa96/display',
		file_get_contents(__DIR__ . '/requests/display.update.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
];
