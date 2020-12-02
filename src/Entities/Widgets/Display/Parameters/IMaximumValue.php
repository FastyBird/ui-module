<?php declare(strict_types = 1);

/**
 * IMaximumValue.php
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
 * Display maximum value parameter interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IMaximumValue
{

	/**
	 * @param float $maximumValue
	 *
	 * @return void
	 */
	public function setMaximumValue(float $maximumValue): void;

	/**
	 * @return float|null
	 */
	public function getMaximumValue(): ?float;

}
