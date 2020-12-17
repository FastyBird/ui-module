<?php declare(strict_types = 1);

/**
 * DisplayHydrator.php
 *
 * @license        More in license.md
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
 */
abstract class DisplayHydrator extends JsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $relationships = [
		Schemas\Widgets\Display\DisplaySchema::RELATIONSHIPS_WIDGET,
	];

	/** @var string */
	protected $translationDomain = 'ui-module.display';

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return int
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydratePrecisionAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): int
	{
		if ($attributes->get('precision') === null || (string) $attributes->get('precision') === '') {
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
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return float
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateMinimumValueAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): float
	{
		if ($attributes->get('minimum_value') === null || (string) $attributes->get('minimum_value') === '') {
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
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return float
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateMaximumValueAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): float
	{
		if ($attributes->get('maximum_value') === null || (string) $attributes->get('maximum_value') === '') {
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
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return float
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateStepValueAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): float
	{
		if ($attributes->get('step_value') === null || (string) $attributes->get('step_value') === '') {
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
