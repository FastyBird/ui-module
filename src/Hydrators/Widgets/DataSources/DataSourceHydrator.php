<?php declare(strict_types = 1);

/**
 * DataSourceHydrator.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           27.05.20
 */

namespace FastyBird\UIModule\Hydrators\Widgets\DataSources;

use FastyBird\JsonApi\Hydrators as JsonApiHydrators;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Schemas;

/**
 * Data source entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template  TEntityClass of Entities\Widgets\DataSources\IDataSource
 * @phpstan-extends   JsonApiHydrators\Hydrator<TEntityClass>
 */
abstract class DataSourceHydrator extends JsonApiHydrators\Hydrator
{

	/** @var string */
	protected string $translationDomain = 'ui-module.dataSources';

	/** @var string[] */
	protected array $relationships = [
		Schemas\Widgets\DataSources\DataSourceSchema::RELATIONSHIPS_WIDGET,
	];

}
