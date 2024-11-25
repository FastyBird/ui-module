<?php declare(strict_types = 1);

/**
 * Repository.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 * @since          1.0.0
 *
 * @date           25.05.20
 */

namespace FastyBird\Module\Ui\Models\Entities\Dashboards;

use Doctrine\ORM;
use Doctrine\Persistence;
use FastyBird\Core\Tools\Exceptions as ToolsExceptions;
use FastyBird\Core\Tools\Helpers as ToolsHelpers;
use FastyBird\Module\Ui\Entities;
use FastyBird\Module\Ui\Exceptions;
use FastyBird\Module\Ui\Queries;
use IPub\DoctrineOrmQuery;
use Nette;
use Ramsey\Uuid;
use Throwable;
use function is_array;

/**
 * Dashboard repository
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Repository
{

	use Nette\SmartObject;

	/** @var array<ORM\EntityRepository<Entities\Dashboards\Dashboard>> */
	private array $repository = [];

	public function __construct(
		private readonly ToolsHelpers\Database $database,
		private readonly Persistence\ManagerRegistry $managerRegistry,
	)
	{
	}

	/**
	 * @template T of Entities\Dashboards\Dashboard
	 *
	 * @param class-string<T> $type
	 *
	 * @return T|null
	 *
	 * @throws ToolsExceptions\InvalidState
	 */
	public function find(
		Uuid\UuidInterface $id,
		string $type = Entities\Dashboards\Dashboard::class,
	): Entities\Dashboards\Dashboard|null
	{
		return $this->database->query(
			fn (): Entities\Dashboards\Dashboard|null => $this->getRepository($type)->find($id),
		);
	}

	/**
	 * @template T of Entities\Dashboards\Dashboard
	 *
	 * @param Queries\Entities\FindDashboards<T> $queryObject
	 * @param class-string<T> $type
	 *
	 * @return T|null
	 *
	 * @throws ToolsExceptions\InvalidState
	 */
	public function findOneBy(
		Queries\Entities\FindDashboards $queryObject,
		string $type = Entities\Dashboards\Dashboard::class,
	): Entities\Dashboards\Dashboard|null
	{
		return $this->database->query(
			fn (): Entities\Dashboards\Dashboard|null => $queryObject->fetchOne($this->getRepository($type)),
		);
	}

	/**
	 * @template T of Entities\Dashboards\Dashboard
	 *
	 * @param class-string<T> $type
	 *
	 * @return array<T>
	 *
	 * @throws ToolsExceptions\InvalidState
	 */
	public function findAll(string $type = Entities\Dashboards\Dashboard::class): array
	{
		return $this->database->query(
			fn (): array => $this->getRepository($type)->findAll(),
		);
	}

	/**
	 * @template T of Entities\Dashboards\Dashboard
	 *
	 * @param Queries\Entities\FindDashboards<T> $queryObject
	 * @param class-string<T> $type
	 *
	 * @return array<T>
	 *
	 * @throws Exceptions\InvalidState
	 */
	public function findAllBy(
		Queries\Entities\FindDashboards $queryObject,
		string $type = Entities\Dashboards\Dashboard::class,
	): array
	{
		try {
			/** @var array<T> $result */
			$result = $this->getResultSet($queryObject, $type)->toArray();

			return $result;
		} catch (Throwable $ex) {
			throw new Exceptions\InvalidState('Fetch all data by query failed', $ex->getCode(), $ex);
		}
	}

	/**
	 * @template T of Entities\Dashboards\Dashboard
	 *
	 * @param Queries\Entities\FindDashboards<T> $queryObject
	 * @param class-string<T> $type
	 *
	 * @return DoctrineOrmQuery\ResultSet<T>
	 *
	 * @throws Exceptions\InvalidState
	 * @throws ToolsExceptions\InvalidState
	 */
	public function getResultSet(
		Queries\Entities\FindDashboards $queryObject,
		string $type = Entities\Dashboards\Dashboard::class,
	): DoctrineOrmQuery\ResultSet
	{
		$result = $this->database->query(
			fn (): DoctrineOrmQuery\ResultSet|array => $queryObject->fetch($this->getRepository($type)),
		);

		if (is_array($result)) {
			throw new Exceptions\InvalidState('Result set could not be created');
		}

		return $result;
	}

	/**
	 * @template T of Entities\Dashboards\Dashboard
	 *
	 * @param class-string<T> $type
	 *
	 * @return ORM\EntityRepository<T>
	 */
	private function getRepository(string $type): ORM\EntityRepository
	{
		if (!isset($this->repository[$type])) {
			$this->repository[$type] = $this->managerRegistry->getRepository($type);
		}

		/** @var ORM\EntityRepository<T> $repository */
		$repository = $this->repository[$type];

		return $repository;
	}

}
