<?php declare(strict_types = 1);

/**
 * Constants.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           09.03.20
 */

namespace FastyBird\UIModule;

/**
 * Service constants
 *
 * @package        FastyBird:UIModule!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Constants
{

	/**
	 * Message bus routing keys
	 */

	// Data routing keys
	public const WS_DEVICES_PROPERTIES_DATA_ROUTING_KEY = 'fb.ws.data.device.property';
	public const WS_DEVICES_CONFIGURATION_DATA_ROUTING_KEY = 'fb.ws.data.device.configuration';
	public const WS_CHANNELS_PROPERTIES_DATA_ROUTING_KEY = 'fb.ws.data.channel.property';
	public const WS_CHANNELS_CONFIGURATION_DATA_ROUTING_KEY = 'fb.ws.data.channel.configuration';

	/**
	 * Service headers
	 */

	public const WS_HEADER_AUTHORIZATION = 'authorization';
	public const WS_HEADER_WS_KEY = 'x-ws-key';
	public const WS_HEADER_ORIGIN = 'origin';

}
