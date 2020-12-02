<?php declare(strict_types = 1);

/**
 * IChartGraph.php
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

namespace FastyBird\UIModule\Entities\Widgets\Display;

use FastyBird\UIModule\Entities;

/**
 * Chart graph display interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IChartGraph extends IDisplay,
	Entities\Widgets\Display\Parameters\IMinimumValue,
	Entities\Widgets\Display\Parameters\IMaximumValue,
	Entities\Widgets\Display\Parameters\IStepValue,
	Entities\Widgets\Display\Parameters\IPrecision
{

	public const DISPLAY_TYPE = 'chartGraph';

	/**
	 * @param bool $state
	 *
	 * @return void
	 */
	public function setEnableMinMax(bool $state): void;

	/**
	 * @return bool
	 */
	public function isEnabledMinMax(): bool;

}
