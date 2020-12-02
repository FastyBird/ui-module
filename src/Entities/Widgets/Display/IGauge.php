<?php declare(strict_types = 1);

/**
 * IGauge.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Entities\Widgets\Display;

use FastyBird\UIModule\Entities;

/**
 * Gauge display interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IGauge extends IDisplay,
	Entities\Widgets\Display\Parameters\IPrecision
{

	public const DISPLAY_TYPE = 'gauge';

}
