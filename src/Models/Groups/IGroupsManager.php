<?php declare(strict_types = 1);

/**
 * IGroupsManager.php
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

namespace FastyBird\UIModule\Models\Groups;

use FastyBird\UIModule\Entities;
use Nette\Utils;

/**
 * Dashboards groups entities manager interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IGroupsManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Groups\IGroup
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Groups\IGroup;

	/**
	 * @param Entities\Groups\IGroup $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Groups\IGroup
	 */
	public function update(
		Entities\Groups\IGroup $entity,
		Utils\ArrayHash $values
	): Entities\Groups\IGroup;

	/**
	 * @param Entities\Groups\IGroup $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Groups\IGroup $entity
	): bool;

}
