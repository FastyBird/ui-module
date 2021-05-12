<?php declare(strict_types = 1);

/**
 * DigitalActuatorWidgetHydrator.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           26.05.20
 */

namespace FastyBird\UIModule\Hydrators\Widgets;

use FastyBird\UIModule\Entities;

/**
 * Digital actuator widget entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DigitalActuatorWidgetHydrator extends WidgetHydrator
{

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Widgets\DigitalActuator::class;
	}

}
