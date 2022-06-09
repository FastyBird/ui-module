<?php declare(strict_types = 1);

namespace Tests\Cases;

use Fig\Http\Message\RequestMethodInterface;
use IPub\SlimRouter;
use IPub\SlimRouter\Http as SlimRouterHttp;
use React\Http\Message\ServerRequest;
use Tester\Assert;
use Tests\Tools;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class DisplayV1ControllerTest extends DbTestCase
{

	/**
	 * @param string $url
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/displayRead.php
	 */
	public function testRead(string $url, int $statusCode, string $fixture): void
	{
		/** @var SlimRouter\Routing\IRouter $router */
		$router = $this->getContainer()->getByType(SlimRouter\Routing\IRouter::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_GET,
			$url
		);

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
		);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(SlimRouterHttp\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/displayUpdate.php
	 */
	public function testUpdate(string $url, string $body, int $statusCode, string $fixture): void
	{
		/** @var SlimRouter\Routing\IRouter $router */
		$router = $this->getContainer()->getByType(SlimRouter\Routing\IRouter::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_PATCH,
			$url,
			[],
			$body
		);

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
		);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(SlimRouterHttp\Response::class, $response);
	}

}

$test_case = new DisplayV1ControllerTest();
$test_case->run();
