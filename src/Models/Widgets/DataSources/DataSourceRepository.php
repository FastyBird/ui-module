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

use Doctrine\ORM;
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

	/**
	 * @var ORM\EntityRepository[]
	 *
	 * @phpstan-var ORM\EntityRepository<Entities\Widgets\DataSources\IDataSource>[]
	 */
	private array $repository = [];

	/** @var Persistence\ManagerRegistry */
	private Persistence\ManagerRegistry $managerRegistry;

	public function __construct(Persistence\ManagerRegistry $managerRegistry)
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
	 * @return ORM\EntityRepository
	 *
	 * @phpstan-param class-string $type
	 *
	 * @phpstan-return ORM\EntityRepository<Entities\Widgets\DataSources\IDataSource>
	 */
	private function getRepository(string $type): ORM\EntityRepository
	{
		if (!isset($this->repository[$type])) {
			$repository = $this->managerRegistry->getRepository($type);

			if (!$repository instanceof ORM\EntityRepository) {
				throw new Exceptions\InvalidStateException('Entity repository could not be loaded');
			}

			$this->repository[$type] = $repository;
		}

		return $this->repository[$type];
	}

}
