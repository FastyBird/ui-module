<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\ModulesMetadata;
use FastyBird\UIModule\Consumers;
use FastyBird\UIModule\Sockets;
use Mockery;
use Nette\Utils;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Psr\Log;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

define('DS', DIRECTORY_SEPARATOR);

/**
 * @testCase
 */
final class ModuleMessageConsumerTest extends BaseMockeryTestCase
{

	/**
	 * @param mixed[] $data
	 * @param mixed[] $expected
	 * @param string $routingKey
	 * @param string $origin
	 *
	 * @dataProvider ./../../../fixtures/Consumers/deviceSuccessfulMessage.php
	 */
	public function testConsumeSuccessfulDeviceMessage(
		array $data,
		array $expected,
		string $routingKey,
		string $origin
	): void {
		$sender = Mockery::mock(Sockets\ISender::class);
		$sender
			->shouldReceive('sendEntity')
			->withArgs(function (string $destination, array $message) use ($expected): bool {
				Assert::same($expected, $message);

				return true;
			})
			->andReturn(true)
			->times(1);

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info')
			->with('[CONSUMER] Successfully consumed entity message', [
				'routing_key' => $routingKey,
				'origin'      => ModulesMetadata\Constants::MODULE_DEVICES_ORIGIN,
				'data'        => $data,
			])
			->times(1);

		$consumer = new Consumers\ModuleMessageConsumer(
			$sender,
			$logger
		);

		$consumer->consume($routingKey, $origin, Utils\ArrayHash::from($data));
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $expected
	 * @param string $routingKey
	 * @param string $origin
	 *
	 * @dataProvider ./../../../fixtures/Consumers/devicePropertySuccessfulMessage.php
	 */
	public function testConsumeSuccessfulDevicePropertyMessage(
		array $data,
		array $expected,
		string $routingKey,
		string $origin
	): void {
		$sender = Mockery::mock(Sockets\ISender::class);
		$sender
			->shouldReceive('sendEntity')
			->withArgs(function (string $destination, array $message) use ($expected): bool {
				Assert::same($expected, $message);

				return true;
			})
			->andReturn(true)
			->times(1);

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info')
			->with('[CONSUMER] Successfully consumed entity message', [
				'routing_key' => $routingKey,
				'origin'      => ModulesMetadata\Constants::MODULE_DEVICES_ORIGIN,
				'data'        => $data,
			])
			->times(1);

		$consumer = new Consumers\ModuleMessageConsumer(
			$sender,
			$logger
		);

		$consumer->consume($routingKey, $origin, Utils\ArrayHash::from($data));
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $expected
	 * @param string $routingKey
	 * @param string $origin
	 *
	 * @dataProvider ./../../../fixtures/Consumers/channelSuccessfulMessage.php
	 */
	public function testConsumeSuccessfulChannelMessage(
		array $data,
		array $expected,
		string $routingKey,
		string $origin
	): void {
		$sender = Mockery::mock(Sockets\ISender::class);
		$sender
			->shouldReceive('sendEntity')
			->withArgs(function (string $destination, array $message) use ($expected): bool {
				Assert::same($expected, $message);

				return true;
			})
			->andReturn(true)
			->times(1);

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info')
			->with('[CONSUMER] Successfully consumed entity message', [
				'routing_key' => $routingKey,
				'origin'      => ModulesMetadata\Constants::MODULE_DEVICES_ORIGIN,
				'data'        => $data,
			])
			->times(1);

		$consumer = new Consumers\ModuleMessageConsumer(
			$sender,
			$logger
		);

		$consumer->consume($routingKey, $origin, Utils\ArrayHash::from($data));
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $expected
	 * @param string $routingKey
	 * @param string $origin
	 *
	 * @dataProvider ./../../../fixtures/Consumers/channelPropertySuccessfulMessage.php
	 */
	public function testConsumeSuccessfulChannelPropertyMessage(
		array $data,
		array $expected,
		string $routingKey,
		string $origin
	): void {
		$sender = Mockery::mock(Sockets\ISender::class);
		$sender
			->shouldReceive('sendEntity')
			->withArgs(function (string $destination, array $message) use ($expected): bool {
				Assert::same($expected, $message);

				return true;
			})
			->andReturn(true)
			->times(1);

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info')
			->with('[CONSUMER] Successfully consumed entity message', [
				'routing_key' => $routingKey,
				'origin'      => ModulesMetadata\Constants::MODULE_DEVICES_ORIGIN,
				'data'        => $data,
			])
			->times(1);

		$consumer = new Consumers\ModuleMessageConsumer(
			$sender,
			$logger
		);

		$consumer->consume($routingKey, $origin, Utils\ArrayHash::from($data));
	}

}

$test_case = new ModuleMessageConsumerTest();
$test_case->run();
