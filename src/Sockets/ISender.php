<?php declare(strict_types = 1);

/**
 * ISender.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Sockets
 * @since          0.1.0
 *
 * @date           08.03.20
 */

namespace FastyBird\UIModule\Sockets;

/**
 * Websockets data sender interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Senders
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface ISender
{

	/**
	 * @param string $destination
	 * @param mixed[] $data
	 *
	 * @return bool
	 */
	public function sendEntity(
		string $destination,
		array $data
	): bool;

}
