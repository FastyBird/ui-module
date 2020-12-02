<?php declare(strict_types = 1);

/**
 * FindDataSourcesQuery.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
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
 * Find widget data sources entities query
 *
 * @package          FastyBird:UIModule!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Widgets\DataSources\DataSource
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindDataSourcesQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	private $filter = [];

	/** @var Closure[] */
	private $select = [];

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return void
	 */
	public function byId(Uuid\UuidInterface $id): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($id): void {
			$qb->andWhere('d.id = :id')->setParameter('id', $id, Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param Entities\Widgets\IWidget $widget
	 *
	 * @return void
	 */
	public function forWidget(Entities\Widgets\IWidget $widget): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('d.widget', 'widget');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($widget): void {
			$qb->andWhere('widget.id = :widget')->setParameter('widget', $widget->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Widgets\DataSources\DataSource> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
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
	 * @param ORM\EntityRepository<Entities\Widgets\DataSources\DataSource> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(d.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Widgets\DataSources\DataSource> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('d');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
