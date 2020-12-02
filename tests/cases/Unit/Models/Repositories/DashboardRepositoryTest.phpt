<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Queries;
use IPub\DoctrineOrmQuery;
use Ramsey\Uuid;
use Tester\Assert;

require_once __DIR__ . '/../../../../bootstrap.php';
require_once __DIR__ . '/../../DbTestCase.php';

/**
 * @testCase
 */
final class DashboardRepositoryTest extends DbTestCase
{

	public function testReadOne(): void
	{
		/** @var Models\Dashboards\DashboardRepository $repository */
		$repository = $this->getContainer()->getByType(Models\Dashboards\DashboardRepository::class);

		$findQuery = new Queries\FindDashboardsQuery();
		$findQuery->byId(Uuid\Uuid::fromString('ab369e71-ada6-4d1a-a5a8-b6ee5cd58296'));

		$entity = $repository->findOneBy($findQuery);

		Assert::true(is_object($entity));
		Assert::type(Entities\Dashboards\Dashboard::class, $entity);
		Assert::same('First floor', $entity->getName());
	}

	public function testReadResultSet(): void
	{
		/** @var Models\Dashboards\DashboardRepository $repository */
		$repository = $this->getContainer()->getByType(Models\Dashboards\DashboardRepository::class);

		$findQuery = new Queries\FindDashboardsQuery();

		$resultSet = $repository->getResultSet($findQuery);

		Assert::type(DoctrineOrmQuery\ResultSet::class, $resultSet);
		Assert::same(2, $resultSet->getTotalCount());
	}

}

$test_case = new DashboardRepositoryTest();
$test_case->run();
