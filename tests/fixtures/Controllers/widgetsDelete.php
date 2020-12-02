<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'delete'        => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96',
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/widgets.delete.json',
	],
	'deleteUnknown' => [
		'/v1/widgets/11553443-4564-454d-af04-0dfeef08aa96',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
];
