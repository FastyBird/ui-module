<?php declare(strict_types = 1);

/**
 * AnalogValueHydrator.php
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

namespace FastyBird\UIModule\Hydrators\Widgets\Displays;

use FastyBird\UIModule\Entities;

/**
 * Analog value widget display entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AnalogValueHydrator extends DisplayHydrator
{

	/** @var string[] */
	protected array $attributes = [
		'precision',
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Widgets\Display\AnalogValue::class;
	}

}
