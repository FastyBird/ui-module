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
final class WidgetRepositoryTest extends DbTestCase
{

	public function testReadOne(): void
	{
		/** @var Models\Widgets\WidgetRepository $repository */
		$repository = $this->getContainer()->getByType(Models\Widgets\WidgetRepository::class);

		$findQuery = new Queries\FindWidgetsQuery();
		$findQuery->byId(Uuid\Uuid::fromString('15553443-4564-454d-af04-0dfeef08aa96'));

		$entity = $repository->findOneBy($findQuery);

		Assert::true(is_object($entity));
		Assert::type(Entities\Widgets\Widget::class, $entity);
		Assert::same('Room temperature', $entity->getName());
	}

	public function testReadResultSet(): void
	{
		/** @var Models\Widgets\WidgetRepository $repository */
		$repository = $this->getContainer()->getByType(Models\Widgets\WidgetRepository::class);

		$findQuery = new Queries\FindWidgetsQuery();

		$resultSet = $repository->getResultSet($findQuery);

		Assert::type(DoctrineOrmQuery\ResultSet::class, $resultSet);
		Assert::same(4, $resultSet->getTotalCount());
	}

}

$test_case = new WidgetRepositoryTest();
$test_case->run();
