<?php declare(strict_types = 1);

/**
 * IDashboardsManager.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Models\Dashboards;

use FastyBird\UIModule\Entities;
use Nette\Utils;

/**
 * Dashboards entities manager interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IDashboardsManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Dashboards\IDashboard
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Dashboards\IDashboard;

	/**
	 * @param Entities\Dashboards\IDashboard $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Dashboards\IDashboard
	 */
	public function update(
		Entities\Dashboards\IDashboard $entity,
		Utils\ArrayHash $values
	): Entities\Dashboards\IDashboard;

	/**
	 * @param Entities\Dashboards\IDashboard $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Dashboards\IDashboard $entity
	): bool;

}
