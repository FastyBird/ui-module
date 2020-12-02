<?php declare(strict_types = 1);

/**
 * IPublisher.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Sockets
 * @since          0.1.0
 *
 * @date           02.12.20
 */

namespace FastyBird\UIModule\Sockets;

/**
 * Websockets data publisher interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Senders
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPublisher
{

	/**
	 * @param string $key
	 * @param mixed[] $data
	 *
	 * @return void
	 */
	public function publish(
		string $key,
		array $data
	): void;

}
