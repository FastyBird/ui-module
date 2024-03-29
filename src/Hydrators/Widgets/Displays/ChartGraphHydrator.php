<?php declare(strict_types = 1);

/**
 * ChartGraphHydrator.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           26.05.20
 */

namespace FastyBird\UIModule\Hydrators\Widgets\Displays;

use FastyBird\JsonApi\Exceptions as JsonApiExceptions;
use FastyBird\UIModule\Entities;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;

/**
 * Chart graph widget display entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends DisplayHydrator<Entities\Widgets\Display\IChartGraph>
 */
final class ChartGraphHydrator extends DisplayHydrator
{

	/** @var string[] */
	protected array $attributes = [
		'minimum_value'  => 'minimumValue',
		'maximum_value'  => 'maximumValue',
		'precision'      => 'precision',
		'step_value'     => 'stepValue',
		'enable_min_max' => 'enableMinMax',
	];

	/**
	 * {@inheritDoc}
	 */
	public function getEntityName(): string
	{
		return Entities\Widgets\Display\ChartGraph::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return bool
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateEnableMinMaxAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): bool
	{
		if (
			!is_scalar($attributes->get('enable_min_max'))
			|| (string) $attributes->get('enable_min_max') === ''
		) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.base.messages.missingRequired.heading'),
				$this->translator->translate('//ui-module.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/enable_min_max',
				]
			);
		}

		return (bool) $attributes->get('enable_min_max');
	}

}
