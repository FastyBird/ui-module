<?php declare(strict_types = 1);

/**
 * SliderHydrator.php
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
 *
 * @phpstan-extends DisplayHydrator<Entities\Widgets\Display\ISlider>
 */
final class SliderHydrator extends DisplayHydrator
{

	/** @var string[] */
	protected array $attributes = [
		'minimum_value' => 'minimumValue',
		'maximum_value' => 'maximumValue',
		'step_value'    => 'stepValue',
		'precision'     => 'precision',
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Widgets\Display\Slider::class;
	}

}
