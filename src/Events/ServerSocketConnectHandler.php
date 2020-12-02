<?php declare(strict_types = 1);

/**
 * ServerSocketConnectHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
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
	private $handlers;

	public function __construct(
		WebSockets\Server\Handlers $handlers
	) {
		$this->handlers = $handlers;
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
		$this->handlers->handleConnect($connection);
	}

}
