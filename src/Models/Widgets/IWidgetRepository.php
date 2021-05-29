<?php declare(strict_types = 1);

/**
 * IWidgetRepository.php
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

namespace FastyBird\UIModule\Models\Widgets;

use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Widget repository interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IWidgetRepository
{

	/**
	 * @param Queries\FindWidgetsQuery $queryObject
	 * @param string $type
	 *
	 * @return Entities\Widgets\IWidget|null
	 *
	 * @phpstan-param class-string $type
	 */
	public function findOneBy(
		Queries\FindWidgetsQuery $queryObject,
		string $type = Entities\Widgets\Widget::class
	): ?Entities\Widgets\IWidget;

	/**
	 * @param Queries\FindWidgetsQuery $queryObject
	 * @param string $type
	 *
	 * @return Entities\Widgets\IWidget[]
	 *
	 * @phpstan-param class-string $type
	 */
	public function findAllBy(
		Queries\FindWidgetsQuery $queryObject,
		string $type = Entities\Widgets\Widget::class
	): array;

	/**
	 * @param Queries\FindWidgetsQuery $queryObject
	 * @param string $type
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-param class-string $type
	 *
	 * @phpstan-return DoctrineOrmQuery\ResultSet<Entities\Widgets\IWidget>
	 */
	public function getResultSet(
		Queries\FindWidgetsQuery $queryObject,
		string $type = Entities\Widgets\Widget::class
	): DoctrineOrmQuery\ResultSet;

}
