<?php declare(strict_types = 1);

/**
 * FindWidgetDataSources.php
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
 * Find widgets data sources configuration query
 *
 * @template T of Documents\Widgets\DataSources\DataSource
 * @extends  QueryObject<T>
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Queries
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class FindWidgetDataSources extends QueryObject
{

	/** @var array<string> */
	protected array $filter = [];

	public function __construct()
	{
		$this->filter[] = '.[?(@.widget != "")]';
	}

	public function byId(Uuid\UuidInterface $id): void
	{
		$this->filter[] = '.[?(@.id =~ /(?i).*^' . $id->toString() . '*$/)]';
	}

	public function forWidget(Documents\Widgets\Widget $widget): void
	{
		$this->filter[] = '.[?(@.widget =~ /(?i).*^' . $widget->getId()->toString() . '*$/)]';
	}

	public function byWidgetId(Uuid\UuidInterface $widgetId): void
	{
		$this->filter[] = '.[?(@.widget =~ /(?i).*^' . $widgetId->toString() . '*$/)]';
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
