<?php declare(strict_types = 1);

/**
 * DataSourceHydrator.php
 *
 * @license        More in license.md
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
use FastyBird\UIModule\Schemas;

/**
 * Data source entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
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
