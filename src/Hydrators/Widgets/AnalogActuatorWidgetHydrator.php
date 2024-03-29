<?php declare(strict_types = 1);

/**
 * AnalogActuatorWidgetHydrator.php
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
 * Analog actuator widget entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends WidgetHydrator<Entities\Widgets\IAnalogActuator>
 */
final class AnalogActuatorWidgetHydrator extends WidgetHydrator
{

	/**
	 * {@inheritDoc}
	 */
	public function getEntityName(): string
	{
		return Entities\Widgets\AnalogActuator::class;
	}

}
