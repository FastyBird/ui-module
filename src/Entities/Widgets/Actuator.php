<?php declare(strict_types = 1);

/**
 * Actuator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Entities\Widgets;

/**
 * Actuator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Actuator extends Widget implements IActuator
{

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string
	{
		return self::WIDGET_GROUP;
	}

}
