<?php declare(strict_types = 1);

/**
 * DigitalSensorWidgetHydrator.php
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
 * Digital sensor widget entity hydrator
 *
 * @package        FastyBird:IOTApiModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends WidgetHydrator<Entities\Widgets\IDigitalSensor>
 */
final class DigitalSensorWidgetHydrator extends WidgetHydrator
{

	/**
	 * {@inheritDoc}
	 */
	public function getEntityName(): string
	{
		return Entities\Widgets\DigitalSensor::class;
	}

}
