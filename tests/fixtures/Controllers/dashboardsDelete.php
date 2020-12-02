<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'delete'        => [
		'/v1/dashboards/272379d8-8351-44b6-ad8d-73a0abcb7f9c',
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/dashboards.delete.json',
	],
	'deleteUnknown' => [
		'/v1/dashboards/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dashboards.notFound.json',
	],
];
