<?php declare(strict_types = 1);

/**
 * IStepValue.php
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

namespace FastyBird\UIModule\Entities\Widgets\Display\Parameters;

/**
 * Display step value parameter interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IStepValue
{

	/**
	 * @param float $stepValue
	 *
	 * @return void
	 */
	public function setStepValue(float $stepValue): void;

	/**
	 * @return float
	 */
	public function getStepValue(): float;

}
