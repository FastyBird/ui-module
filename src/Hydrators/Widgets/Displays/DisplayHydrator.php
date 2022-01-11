<?php declare(strict_types = 1);

/**
 * DisplayHydrator.php
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
use FastyBird\JsonApi\Hydrators as JsonApiHydrators;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Schemas;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;

/**
 * Widget entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template  TEntityClass of Entities\Widgets\Display\IDisplay
 * @phpstan-extends   JsonApiHydrators\Hydrator<TEntityClass>
 */
abstract class DisplayHydrator extends JsonApiHydrators\Hydrator
{

	/** @var string[] */
	protected array $relationships = [
		Schemas\Widgets\Display\DisplaySchema::RELATIONSHIPS_WIDGET,
	];

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return int
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydratePrecisionAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): int
	{
		if (
			!is_scalar($attributes->get('precision'))
			|| (string) $attributes->get('precision') === ''
		) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.base.messages.missingRequired.heading'),
				$this->translator->translate('//ui-module.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/precision',
				]
			);
		}

		return (int) $attributes->get('precision');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return float
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateMinimumValueAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): float
	{
		if (
			!is_scalar($attributes->get('minimum_value'))
			|| (string) $attributes->get('minimum_value') === ''
		) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.base.messages.missingRequired.heading'),
				$this->translator->translate('//ui-module.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/minimum_value',
				]
			);
		}

		return (float) $attributes->get('minimum_value');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return float
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateMaximumValueAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): float
	{
		if (
			!is_scalar($attributes->get('maximum_value'))
			|| (string) $attributes->get('maximum_value') === ''
		) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.base.messages.missingRequired.heading'),
				$this->translator->translate('//ui-module.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/maximum_value',
				]
			);
		}

		return (float) $attributes->get('maximum_value');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return float
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateStepValueAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): float
	{
		if (
			!is_scalar($attributes->get('step_value'))
			|| (string) $attributes->get('step_value') === ''
		) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.base.messages.missingRequired.heading'),
				$this->translator->translate('//ui-module.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/step_value',
				]
			);
		}

		return (float) $attributes->get('step_value');
	}

}
