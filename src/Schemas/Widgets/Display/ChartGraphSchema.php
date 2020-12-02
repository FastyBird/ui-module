<?php declare(strict_types = 1);

/**
 * ChartGraphSchema.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           26.05.20
 */

namespace FastyBird\UIModule\Schemas\Widgets\Display;

use FastyBird\UIModule\Entities;
use Neomerx\JsonApi;

/**
 * Chart graph widget display entity schema
 *
 * @package         FastyBird:UIModule!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Widgets\Display\IChartGraph
 * @phpstan-extends DisplaySchema<T>
 */
final class ChartGraphSchema extends DisplaySchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'ui-module/widget-display-chart-graph';

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Widgets\Display\ChartGraph::class;
	}

	/**
	 * @param Entities\Widgets\Display\IChartGraph $display
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($display, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return array_merge((array) parent::getAttributes($display, $context), [
			'minimum_value'  => $display->getMinimumValue(),
			'maximum_value'  => $display->getMaximumValue(),
			'precision'      => $display->getPrecision(),
			'step_value'     => $display->getStepValue(),
			'enable_min_max' => $display->isEnabledMinMax(),
		]);
	}

}
