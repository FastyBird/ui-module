<?php declare(strict_types = 1);

/**
 * IDisplaysManager.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Models\Widgets\Displays;

use FastyBird\UIModule\Entities;
use Nette\Utils;

/**
 * Widgets displays entities manager interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IDisplaysManager
{

	/**
	 * @param Entities\Widgets\Display\IDisplay $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Widgets\Display\IDisplay
	 */
	public function update(
		Entities\Widgets\Display\IDisplay $entity,
		Utils\ArrayHash $values
	): Entities\Widgets\Display\IDisplay;

}
