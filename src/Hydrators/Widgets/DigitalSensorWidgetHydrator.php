<?php declare(strict_types = 1);

/**
 * DigitalSensorWidgetHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
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
 */
final class DigitalSensorWidgetHydrator extends WidgetHydrator
{

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Widgets\DigitalSensor::class;
	}

}
