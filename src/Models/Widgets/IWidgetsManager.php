<?php declare(strict_types = 1);

/**
 * IWidgetsManager.php
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

namespace FastyBird\UIModule\Models\Widgets;

use FastyBird\UIModule\Entities;
use Nette\Utils;

/**
 * Widgets entities manager interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IWidgetsManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Widgets\IWidget
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Widgets\IWidget;

	/**
	 * @param Entities\Widgets\IWidget $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Widgets\IWidget
	 */
	public function update(
		Entities\Widgets\IWidget $entity,
		Utils\ArrayHash $values
	): Entities\Widgets\IWidget;

	/**
	 * @param Entities\Widgets\IWidget $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Widgets\IWidget $entity
	): bool;

}
