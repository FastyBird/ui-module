<?php declare(strict_types = 1);

/**
 * IDashboardRepository.php
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
use FastyBird\UIModule\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Dashboard repository interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IDashboardRepository
{

	/**
	 * @param Queries\FindDashboardsQuery $queryObject
	 *
	 * @return Entities\Dashboards\IDashboard|null
	 */
	public function findOneBy(Queries\FindDashboardsQuery $queryObject): ?Entities\Dashboards\IDashboard;

	/**
	 * @param Queries\FindDashboardsQuery $queryObject
	 *
	 * @return Entities\Dashboards\IDashboard[]
	 */
	public function findAllBy(Queries\FindDashboardsQuery $queryObject): array;

	/**
	 * @param Queries\FindDashboardsQuery $queryObject
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-return DoctrineOrmQuery\ResultSet<Entities\Dashboards\IDashboard>
	 */
	public function getResultSet(Queries\FindDashboardsQuery $queryObject): DoctrineOrmQuery\ResultSet;

}
