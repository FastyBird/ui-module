<?php declare(strict_types = 1);

/**
 * GroupedButtonHydrator.php
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
use FastyBird\UIModule\Types;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;

/**
 * Grouped button widget display entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends DisplayHydrator<Entities\Widgets\Display\IGroupedButton>
 */
final class GroupedButtonHydrator extends DisplayHydrator
{

	/** @var string[] */
	protected array $attributes = [
		'icon',
	];

	/**
	 * {@inheritDoc}
	 */
	public function getEntityName(): string
	{
		return Entities\Widgets\Display\GroupedButton::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return Types\WidgetIconType
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateIconAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): Types\WidgetIconType
	{
		if (
			!is_scalar($attributes->get('icon'))
			|| (string) $attributes->get('icon') === ''
		) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.base.messages.missingRequired.heading'),
				$this->translator->translate('//ui-module.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/icon',
				]
			);
		}

		if (!Types\WidgetIconType::isValidValue($attributes->get('icon'))) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.widgets.messages.invalidValue.heading'),
				$this->translator->translate('//ui-module.widgets.messages.invalidValue.message'),
				[
					'pointer' => '/data/attributes/icon',
				]
			);
		}

		return Types\WidgetIconType::get($attributes->get('icon'));
	}

}
