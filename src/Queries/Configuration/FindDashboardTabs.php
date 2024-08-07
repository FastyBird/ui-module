<?php declare(strict_types = 1);

/**
 * FindDashboardTabs.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Queries
 * @since          1.0.0
 *
 * @date           05.08.24
 */

namespace FastyBird\Module\Ui\Queries\Configuration;

use FastyBird\Module\Ui\Documents;
use FastyBird\Module\Ui\Exceptions;
use Flow\JSONPath;
use Nette\Utils;
use Ramsey\Uuid;
use function serialize;

/**
 * Find dashboards tabs configuration query
 *
 * @template T of Documents\Dashboards\Tabs\Tab
 * @extends  QueryObject<T>
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Queries
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class FindDashboardTabs extends QueryObject
{

	/** @var array<string> */
	private array $filter = [];

	public function __construct()
	{
		$this->filter[] = '.[?(@.dashboard != "")]';
	}

	public function byId(Uuid\UuidInterface $id): void
	{
		$this->filter[] = '.[?(@.id =~ /(?i).*^' . $id->toString() . '*$/)]';
	}

	public function byIdentifier(string $identifier): void
	{
		$this->filter[] = '.[?(@.identifier =~ /(?i).*^' . $identifier . '*$/)]';
	}

	public function byName(string|null $name): void
	{
		$this->filter[] = $name === null ? '.[?(@.name == null)]' : '.[?(@.name =~ /(?i).*^' . $name . '*$/)]';
	}

	public function forDashboard(Documents\Dashboards\Dashboard $dashboard): void
	{
		$this->filter[] = '.[?(@.dashboard =~ /(?i).*^' . $dashboard->getId()->toString() . '*$/)]';
	}

	public function byDashboardId(Uuid\UuidInterface $dashboardId): void
	{
		$this->filter[] = '.[?(@.dashboard =~ /(?i).*^' . $dashboardId->toString() . '*$/)]';
	}

	/**
	 * @throws JSONPath\JSONPathException
	 */
	protected function doCreateQuery(JSONPath\JSONPath $repository): JSONPath\JSONPath
	{
		$filtered = $repository;

		foreach ($this->filter as $filter) {
			$filtered = $filtered->find($filter);
		}

		return $filtered;
	}

	/**
	 * @throws Exceptions\InvalidState
	 */
	public function toString(): string
	{
		try {
			return serialize(Utils\Json::encode($this->filter));
		} catch (Utils\JsonException) {
			throw new Exceptions\InvalidState('Cache key could not be generated');
		}
	}

}
