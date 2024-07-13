<?php declare(strict_types = 1);

namespace FastyBird\Module\Ui\Tests\Cases\Unit\Models\Entities\Repositories;

use Error;
use FastyBird\Library\Application\Exceptions as ApplicationExceptions;
use FastyBird\Module\Ui\Exceptions;
use FastyBird\Module\Ui\Models;
use FastyBird\Module\Ui\Queries;
use FastyBird\Module\Ui\Tests;
use IPub\DoctrineOrmQuery\Exceptions as DoctrineOrmQueryExceptions;
use Nette;
use Ramsey\Uuid;
use RuntimeException;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class WidgetsRepositoryTest extends Tests\Cases\Unit\DbTestCase
{

	/**
	 * @throws ApplicationExceptions\InvalidArgument
	 * @throws DoctrineOrmQueryExceptions\QueryException
	 * @throws Exceptions\InvalidArgument
	 * @throws Nette\DI\MissingServiceException
	 * @throws RuntimeException
	 * @throws Error
	 */
	public function testFindOne(): void
	{
		$repository = $this->getContainer()->getByType(Models\Entities\Widgets\Repository::class);

		$entity = $repository->find(Uuid\Uuid::fromString('15553443-4564-454d-af04-0dfeef08aa96'));

		self::assertIsObject($entity);
		self::assertSame('Room temperature', $entity->getName());
	}

	/**
	 * @throws ApplicationExceptions\InvalidArgument
	 * @throws DoctrineOrmQueryExceptions\QueryException
	 * @throws Exceptions\InvalidArgument
	 * @throws Nette\DI\MissingServiceException
	 * @throws RuntimeException
	 * @throws Error
	 */
	public function testReadOne(): void
	{
		$repository = $this->getContainer()->getByType(Models\Entities\Widgets\Repository::class);

		$findQuery = new Queries\Entities\FindWidgets();
		$findQuery->byId(Uuid\Uuid::fromString('15553443-4564-454d-af04-0dfeef08aa96'));

		$entity = $repository->findOneBy($findQuery);

		self::assertIsObject($entity);
		self::assertSame('Room temperature', $entity->getName());
	}

	/**
	 * @throws ApplicationExceptions\InvalidArgument
	 * @throws DoctrineOrmQueryExceptions\QueryException
	 * @throws Exceptions\InvalidArgument
	 * @throws Nette\DI\MissingServiceException
	 * @throws RuntimeException
	 * @throws Error
	 */
	public function testReadResultSet(): void
	{
		$repository = $this->getContainer()->getByType(Models\Entities\Widgets\Repository::class);

		$findQuery = new Queries\Entities\FindWidgets();

		$resultSet = $repository->getResultSet($findQuery);

		self::assertSame(4, $resultSet->getTotalCount());
	}

}
