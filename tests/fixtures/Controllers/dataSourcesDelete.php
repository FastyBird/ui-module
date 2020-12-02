<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'delete'            => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377',
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/dataSources.delete.json',
	],
	'deleteUnknown'     => [
		'/v1/widgets/15553443-4564-454d-af04-0dfeef08aa96/data-sources/774937a7-8565-472e-8e12-fe97cd55a377',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/dataSources.notFound.json',
	],
	'dashboardNotFound' => [
		'/v1/widgets/11553443-4564-454d-af04-0dfeef08aa96/data-sources/764937a7-8565-472e-8e12-fe97cd55a377',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/widgets.notFound.json',
	],
];
