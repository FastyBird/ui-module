<?php declare(strict_types = 1);

/**
 * GroupRepository.php
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

namespace FastyBird\UIModule\Models\Groups;

use Doctrine\Common;
use Doctrine\Persistence;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Exceptions;
use FastyBird\UIModule\Queries;
use IPub\DoctrineOrmQuery;
use Nette;
use Throwable;

/**
 * Dashboard group repository
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class GroupRepository implements IGroupRepository
{

	use Nette\SmartObject;

	/** @var Persistence\ObjectRepository<Entities\Groups\Group>|null */
	public $repository = null;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(Queries\FindGroupsQuery $queryObject): ?Entities\Groups\IGroup
	{
		/** @var Entities\Groups\IGroup|null $group */
		$group = $queryObject->fetchOne($this->getRepository());

		return $group;
	}

	/**
	 * @return Persistence\ObjectRepository<Entities\Groups\Group>
	 */
	private function getRepository(): Persistence\ObjectRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Groups\Group::class);
		}

		return $this->repository;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function findAllBy(Queries\FindGroupsQuery $queryObject): array
	{
		$result = $queryObject->fetch($this->getRepository());

		return is_array($result) ? $result : $result->toArray();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function getResultSet(
		Queries\FindGroupsQuery $queryObject
	): DoctrineOrmQuery\ResultSet {
		$result = $queryObject->fetch($this->getRepository());

		if (!$result instanceof DoctrineOrmQuery\ResultSet) {
			throw new Exceptions\InvalidStateException('Result set for given query could not be loaded.');
		}

		return $result;
	}

}
