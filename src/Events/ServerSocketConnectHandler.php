<?php declare(strict_types = 1);

/**
 * ServerSocketConnectHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           02.12.20
 */

namespace FastyBird\UIModule\Events;

use IPub\WebSockets;
use Nette;
use Nette\Utils;
use React\Socket;

/**
 * Socket server client connect handler
 *
 * @package         FastyBird:UIModule!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ServerSocketConnectHandler
{

	use Nette\SmartObject;

	/** @var WebSockets\Server\Handlers */
	private WebSockets\Server\Handlers $handlers;

	/** @var WebSockets\Server\Configuration */
	private WebSockets\Server\Configuration $configuration;

	public function __construct(
		WebSockets\Server\Handlers $handlers,
		WebSockets\Server\Configuration $configuration
	) {
		$this->handlers = $handlers;
		$this->configuration = $configuration;
	}

	/**
	 * @param Socket\ConnectionInterface $connection
	 *
	 * @return void
	 *
	 * @throws WebSockets\Exceptions\StorageException
	 */
	public function __invoke(Socket\ConnectionInterface $connection): void
	{
		if ($connection->getLocalAddress() === null) {
			return;
		}

		$parsed = Utils\ArrayHash::from((array) parse_url($connection->getLocalAddress()));

		if ($parsed->offsetExists('port') && $parsed->offsetGet('port') === $this->configuration->getPort()) {
			$this->handlers->handleConnect($connection);
		}
	}

}
