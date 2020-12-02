<?php declare(strict_types = 1);

/**
 * WidgetIconType.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Types
 * @since          0.1.0
 *
 * @date           24.09.18
 */

namespace FastyBird\UIModule\Types;

use Consistence;

/**
 * Doctrine2 DB type for widget icon column
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class WidgetIconType extends Consistence\Enum\Enum
{

	/**
	 * Define icons
	 */
	public const ICON_THERMOMETER = 'thermometer';
	public const ICON_LIGHTING = 'lighting';
	public const ICON_VALVE = 'valve';
	public const ICON_MOTOR = 'motor';
	public const ICON_LOCK = 'lock';
	public const ICON_PLUG = 'plug';
	public const ICON_BUTTON = 'button';
	public const ICON_HUMIDITY = 'humidity';
	public const ICON_LUMINOSITY = 'luminosity';
	public const ICON_FAN = 'fan';
	public const ICON_MIC = 'mic';
	public const ICON_LED = 'led';
	public const ICON_GAUGE = 'gauge';
	public const ICON_KNOB = 'knob';
	public const ICON_MOTION = 'motion';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

}
