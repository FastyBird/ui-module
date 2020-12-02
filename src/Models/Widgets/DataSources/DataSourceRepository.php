<?php declare(strict_types = 1);

/**
 * IDataSourceRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Models\Widgets\DataSources;

use Doctrine\Common;
use Doctrine\Persistence;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Exceptions;
use FastyBird\UIModule\Queries;
use IPub\DoctrineOrmQuery;
use Nette;
use Throwable;

/**
 * Widget data source repository
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DataSourceRepository implements IDataSourceRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Persistence\ObjectRepository<Entities\Widgets\DataSources\DataSource>[] */
	private $repository = [];

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(
		Queries\FindDataSourcesQuery $queryObject,
		string $type = Entities\Widgets\DataSources\DataSource::class
	): ?Entities\Widgets\DataSources\IDataSource {
		/** @var Entities\Widgets\DataSources\IDataSource|null $dataSource */
		$dataSource = $queryObject->fetchOne($this->getRepository($type));

		return $dataSource;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function findAllBy(
		Queries\FindDataSourcesQuery $queryObject,
		string $type = Entities\Widgets\DataSources\DataSource::class
	): array {
		$result = $queryObject->fetch($this->getRepository($type));

		return is_array($result) ? $result : $result->toArray();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function getResultSet(
		Queries\FindDataSourcesQuery $queryObject,
		string $type = Entities\Widgets\DataSources\DataSource::class
	): DoctrineOrmQuery\ResultSet {
		$result = $queryObject->fetch($this->getRepository($type));

		if (!$result instanceof DoctrineOrmQuery\ResultSet) {
			throw new Exceptions\InvalidStateException('Result set for given query could not be loaded.');
		}

		return $result;
	}

	/**
	 * @param string $type
	 *
	 * @return Persistence\ObjectRepository<Entities\Widgets\DataSources\DataSource>
	 *
	 * @phpstan-template T of Entities\Widgets\DataSources\DataSource
	 * @phpstan-param    class-string<T> $type
	 */
	private function getRepository(string $type): Persistence\ObjectRepository
	{
		if (!isset($this->repository[$type])) {
			$this->repository[$type] = $this->managerRegistry->getRepository($type);
		}

		return $this->repository[$type];
	}

}
