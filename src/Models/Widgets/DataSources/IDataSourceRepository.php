<?php declare(strict_types = 1);

/**
 * IDataSourceRepository.php
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

namespace FastyBird\UIModule\Models\Widgets\DataSources;

use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Widget data source repository interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IDataSourceRepository
{

	/**
	 * @param Queries\FindDataSourcesQuery $queryObject
	 * @param string $type
	 *
	 * @return Entities\Widgets\DataSources\IDataSource|null
	 *
	 * @phpstan-param class-string $type
	 */
	public function findOneBy(
		Queries\FindDataSourcesQuery $queryObject,
		string $type = Entities\Widgets\DataSources\DataSource::class
	): ?Entities\Widgets\DataSources\IDataSource;

	/**
	 * @param Queries\FindDataSourcesQuery $queryObject
	 * @param string $type
	 *
	 * @return Entities\Widgets\DataSources\IDataSource[]
	 *
	 * @phpstan-param class-string $type
	 */
	public function findAllBy(
		Queries\FindDataSourcesQuery $queryObject,
		string $type = Entities\Widgets\DataSources\DataSource::class
	): array;

	/**
	 * @param Queries\FindDataSourcesQuery $queryObject
	 * @param string $type
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-param class-string $type
	 *
	 * @phpstan-return DoctrineOrmQuery\ResultSet<Entities\Widgets\DataSources\IDataSource>
	 */
	public function getResultSet(
		Queries\FindDataSourcesQuery $queryObject,
		string $type = Entities\Widgets\DataSources\DataSource::class
	): DoctrineOrmQuery\ResultSet;

}
