<?php declare(strict_types = 1);

/**
 * IPrecision.php
 *
 * @license        More in LICENSE.md
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
 * Display precision parameter interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPrecision
{

	/**
	 * @param int $precision
	 *
	 * @return void
	 */
	public function setPrecision(int $precision): void;

	/**
	 * @return int
	 */
	public function getPrecision(): int;

}
