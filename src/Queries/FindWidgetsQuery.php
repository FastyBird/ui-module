<?php declare(strict_types = 1);

/**
 * FindWidgetsQuery.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Queries
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Queries;

use Closure;
use Doctrine\ORM;
use FastyBird\UIModule\Entities;
use IPub\DoctrineOrmQuery;
use Ramsey\Uuid;

/**
 * Find widgets entities query
 *
 * @package          FastyBird:UIModule!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends DoctrineOrmQuery\QueryObject<Entities\Widgets\IWidget>
 */
class FindWidgetsQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	private array $filter = [];

	/** @var Closure[] */
	private array $select = [];

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return void
	 */
	public function byId(Uuid\UuidInterface $id): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($id): void {
			$qb->andWhere('w.id = :id')->setParameter('id', $id, Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param Entities\Groups\IGroup $group
	 *
	 * @return void
	 */
	public function inGroup(Entities\Groups\IGroup $group): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('w.groups', 'g');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($group): void {
			$qb->andWhere('g.id = :group')->setParameter('group', $group->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param Entities\Widgets\DataSources\IDataSource $dataSource
	 *
	 * @return void
	 */
	public function withDataSource(Entities\Widgets\DataSources\IDataSource $dataSource): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->addSelect('dataSources');
			$qb->leftJoin('w.dataSources', 'dataSources');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($dataSource): void {
			$qb->andWhere('dataSources.id = :dataSource')->setParameter('dataSource', $dataSource->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param ORM\EntityRepository $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<Entities\Widgets\IWidget> $repository
	 */
	protected function doCreateQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository);

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<Entities\Widgets\IWidget> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('w');
		$qb->addSelect('display');
		$qb->join('w.display', 'display');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<Entities\Widgets\IWidget> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(w.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
