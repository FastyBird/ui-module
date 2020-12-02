<?php declare(strict_types = 1);

/**
 * ExchangeController.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           01.05.20
 */

namespace FastyBird\UIModule\Controllers;

use FastyBird\ModulesMetadata;
use FastyBird\ModulesMetadata\Exceptions as ModulesMetadataExceptions;
use FastyBird\ModulesMetadata\Loaders as ModulesMetadataLoaders;
use FastyBird\ModulesMetadata\Schemas as ModulesMetadataSchemas;
use FastyBird\UIModule;
use FastyBird\UIModule\Exceptions;
use FastyBird\UIModule\Sockets;
use IPub\WebSockets;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Exchange sockets controller
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ExchangeController extends WebSockets\Application\Controller\Controller
{

	private const DEVICE_PROPERTY_SCHEMA_FILENAME = 'data.device.property.json';
	private const DEVICE_CONFIGURATION_SCHEMA_FILENAME = 'data.device.configuration.json';
	private const CHANNEL_PROPERTY_SCHEMA_FILENAME = 'data.channel.property.json';
	private const CHANNEL_CONFIGURATION_SCHEMA_FILENAME = 'data.channel.configuration.json';

	/** @var Sockets\IPublisher|null */
	private $publisher;

	/** @var ModulesMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var ModulesMetadataSchemas\IValidator */
	private $jsonValidator;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		ModulesMetadataLoaders\ISchemaLoader $schemaLoader,
		ModulesMetadataSchemas\IValidator $jsonValidator,
		?Sockets\IPublisher $publisher,
		?Log\LoggerInterface $logger
	) {
		parent::__construct();

		$this->schemaLoader = $schemaLoader;
		$this->publisher = $publisher;
		$this->jsonValidator = $jsonValidator;
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * @param mixed[] $args
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function actionCall(
		array $args
	): void {
		if (!array_key_exists('routing_key', $args)) {
			throw new Exceptions\InvalidArgumentException('Provided message has invalid format');
		}

		switch ($args['routing_key']) {
			case UIModule\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY:
				$schema = $this->schemaLoader->load(ModulesMetadata\Constants::RESOURCES_FOLDER . '/schemas/data/' . self::DEVICE_PROPERTY_SCHEMA_FILENAME);

				$data = $this->parse($args, $schema);

				if ($this->publisher !== null) {
					$this->publisher->publish(
						$args['routing_key'],
						[
							'device'   => $data->offsetGet('device'),
							'property' => $data->offsetGet('property'),
							'expected' => $data->offsetGet('expected'),
						]
					);
				}
				break;

			case UIModule\Constants::RABBIT_MQ_DEVICES_CONFIGURATION_DATA_ROUTING_KEY:
				$schema = $this->schemaLoader->load(ModulesMetadata\Constants::RESOURCES_FOLDER . '/schemas/data/' . self::DEVICE_CONFIGURATION_SCHEMA_FILENAME);

				$data = $this->parse($args, $schema);

				if ($this->publisher !== null) {
					$this->publisher->publish(
						$args['routing_key'],
						[
							'device'   => $data->offsetGet('device'),
							'name'     => $data->offsetGet('name'),
							'expected' => $data->offsetGet('expected'),
						]
					);
				}
				break;

			case UIModule\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY:
				$schema = $this->schemaLoader->load(ModulesMetadata\Constants::RESOURCES_FOLDER . '/schemas/data/' . self::CHANNEL_PROPERTY_SCHEMA_FILENAME);

				$data = $this->parse($args, $schema);

				if ($this->publisher !== null) {
					$this->publisher->publish(
						$args['routing_key'],
						[
							'device'   => $data->offsetGet('device'),
							'channel'  => $data->offsetGet('channel'),
							'property' => $data->offsetGet('property'),
							'expected' => $data->offsetGet('expected'),
						]
					);
				}
				break;

			case UIModule\Constants::RABBIT_MQ_CHANNELS_CONFIGURATION_DATA_ROUTING_KEY:
				$schema = $this->schemaLoader->load(ModulesMetadata\Constants::RESOURCES_FOLDER . '/schemas/data/' . self::CHANNEL_CONFIGURATION_SCHEMA_FILENAME);

				$data = $this->parse($args, $schema);

				if ($this->publisher !== null) {
					$this->publisher->publish(
						$args['routing_key'],
						[
							'device'   => $data->offsetGet('device'),
							'channel'  => $data->offsetGet('channel'),
							'name'     => $data->offsetGet('name'),
							'expected' => $data->offsetGet('expected'),
						]
					);
				}
				break;

			default:
				throw new Exceptions\InvalidArgumentException('Provided message has unknown routing key');
		}

		$this->payload->data = [
			'response' => 'accepted',
		];
	}

	/**
	 * @param mixed[] $data
	 * @param string $schema
	 *
	 * @return Utils\ArrayHash
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	private function parse(
		array $data,
		string $schema
	): Utils\ArrayHash {
		try {
			return $this->jsonValidator->validate(Utils\Json::encode($data), $schema);

		} catch (Utils\JsonException $ex) {
			$this->logger->error('[PARSER] Received message could not be validated', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new Exceptions\InvalidArgumentException('Provided data are not valid json format', 0, $ex);

		} catch (ModulesMetadataExceptions\InvalidDataException $ex) {
			$this->logger->debug('[PARSER] Received message is not valid', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new Exceptions\InvalidArgumentException('Provided data are not in valid structure', 0, $ex);

		} catch (Throwable $ex) {
			$this->logger->error('[PARSER] Received message is not valid', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new Exceptions\InvalidArgumentException('Provided data could not be validated', 0, $ex);
		}
	}

}
