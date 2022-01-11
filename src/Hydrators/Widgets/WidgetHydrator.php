<?php declare(strict_types = 1);

/**
 * WidgetHydrator.php
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

namespace FastyBird\UIModule\Hydrators\Widgets;

use Contributte\Translation;
use Doctrine\Persistence;
use FastyBird\JsonApi\Exceptions as JsonApiExceptions;
use FastyBird\JsonApi\Hydrators as JsonApiHydrators;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Queries;
use FastyBird\UIModule\Schemas;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;
use Ramsey\Uuid;

/**
 * Widget entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template  TEntityClass of Entities\Widgets\IWidget
 * @phpstan-extends   JsonApiHydrators\Hydrator<TEntityClass>
 */
abstract class WidgetHydrator extends JsonApiHydrators\Hydrator
{

	/** @var string[] */
	protected array $attributes = [
		0 => 'name',

		'minimum_value'  => 'minimumValue',
		'maximum_value'  => 'maximumValue',
		'step_value'     => 'stepValue',
		'precision'      => 'precision',
		'enable_min_max' => 'enableMinMax',
	];

	/** @var string[] */
	protected array $relationships = [
		0                                                        => Schemas\Widgets\WidgetSchema::RELATIONSHIPS_DISPLAY,
		1                                                        => Schemas\Widgets\WidgetSchema::RELATIONSHIPS_GROUPS,
		Schemas\Widgets\WidgetSchema::RELATIONSHIPS_DATA_SOURCES => 'dataSources',
	];

	/** @var Models\Groups\IGroupRepository */
	private Models\Groups\IGroupRepository $groupRepository;

	/**
	 * @param Models\Groups\IGroupRepository $groupRepository
	 * @param Persistence\ManagerRegistry $managerRegistry
	 * @param Translation\Translator $translator
	 */
	public function __construct(
		Models\Groups\IGroupRepository $groupRepository,
		Persistence\ManagerRegistry $managerRegistry,
		Translation\Translator $translator
	) {
		parent::__construct($managerRegistry, $translator);

		$this->groupRepository = $groupRepository;
	}

	/**
	 * @param JsonAPIDocument\Objects\IRelationshipObject $relationship
	 * @param JsonAPIDocument\Objects\IResourceObjectCollection|null $included
	 *
	 * @return mixed[]|null
	 */
	protected function hydrateDisplayRelationship(
		JsonAPIDocument\Objects\IRelationshipObject $relationship,
		?JsonAPIDocument\Objects\IResourceObjectCollection $included
	): ?array {
		if (!$relationship->isHasOne()) {
			return null;
		}

		if ($included !== null) {
			foreach ($included->getAll() as $item) {
				if (
					$relationship->getIdentifier() !== null
					&& $item->getId() === $relationship->getIdentifier()->getId()
				) {
					$result = $this->buildDisplay($item->getType(), $item->getAttributes(), $item->getId());

					if ($result !== null) {
						return $result;
					}
				}
			}
		}

		return null;
	}

	/**
	 * @param string $type
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 * @param string|null $identifier
	 *
	 * @return mixed[]|null
	 */
	private function buildDisplay(
		string $type,
		JsonAPIDocument\Objects\IStandardObject $attributes,
		?string $identifier = null
	): ?array {
		switch ($type) {
			case Schemas\Widgets\Display\AnalogValueSchema::SCHEMA_TYPE:
				$entityMapping = $this->mapEntity(Entities\Widgets\Display\AnalogValue::class);

				$display = $this->hydrateAttributes(
					Entities\Widgets\Display\AnalogValue::class,
					$attributes,
					$entityMapping,
					null,
					null
				);

				$display['entity'] = Entities\Widgets\Display\AnalogValue::class;
				$display[self::IDENTIFIER_KEY] = $identifier !== null && $identifier !== '' ? Uuid\Uuid::fromString($identifier) : $identifier;

				return $display;

			case Schemas\Widgets\Display\ButtonSchema::SCHEMA_TYPE:
				$entityMapping = $this->mapEntity(Entities\Widgets\Display\Button::class);

				$display = $this->hydrateAttributes(
					Entities\Widgets\Display\Button::class,
					$attributes,
					$entityMapping,
					null,
					null
				);

				$display['entity'] = Entities\Widgets\Display\Button::class;
				$display[self::IDENTIFIER_KEY] = $identifier !== null && $identifier !== '' ? Uuid\Uuid::fromString($identifier) : $identifier;

				return $display;

			case Schemas\Widgets\Display\ChartGraphSchema::SCHEMA_TYPE:
				$entityMapping = $this->mapEntity(Entities\Widgets\Display\ChartGraph::class);

				$display = $this->hydrateAttributes(
					Entities\Widgets\Display\ChartGraph::class,
					$attributes,
					$entityMapping,
					null,
					null
				);

				$display['entity'] = Entities\Widgets\Display\ChartGraph::class;
				$display[self::IDENTIFIER_KEY] = $identifier !== null && $identifier !== '' ? Uuid\Uuid::fromString($identifier) : $identifier;

				return $display;

			case Schemas\Widgets\Display\DigitalValueSchema::SCHEMA_TYPE:
				$entityMapping = $this->mapEntity(Entities\Widgets\Display\DigitalValue::class);

				$display = $this->hydrateAttributes(
					Entities\Widgets\Display\DigitalValue::class,
					$attributes,
					$entityMapping,
					null,
					null
				);

				$display['entity'] = Entities\Widgets\Display\DigitalValue::class;
				$display[self::IDENTIFIER_KEY] = $identifier !== null && $identifier !== '' ? Uuid\Uuid::fromString($identifier) : $identifier;

				return $display;

			case Schemas\Widgets\Display\GaugeSchema::SCHEMA_TYPE:
				$entityMapping = $this->mapEntity(Entities\Widgets\Display\Gauge::class);

				$display = $this->hydrateAttributes(
					Entities\Widgets\Display\Gauge::class,
					$attributes,
					$entityMapping,
					null,
					null
				);

				$display['entity'] = Entities\Widgets\Display\Gauge::class;
				$display[self::IDENTIFIER_KEY] = $identifier !== null && $identifier !== '' ? Uuid\Uuid::fromString($identifier) : $identifier;

				return $display;

			case Schemas\Widgets\Display\GroupedButtonSchema::SCHEMA_TYPE:
				$entityMapping = $this->mapEntity(Entities\Widgets\Display\GroupedButton::class);

				$display = $this->hydrateAttributes(
					Entities\Widgets\Display\GroupedButton::class,
					$attributes,
					$entityMapping,
					null,
					null
				);

				$display['entity'] = Entities\Widgets\Display\GroupedButton::class;
				$display[self::IDENTIFIER_KEY] = $identifier !== null && $identifier !== '' ? Uuid\Uuid::fromString($identifier) : $identifier;

				return $display;

			case Schemas\Widgets\Display\SliderSchema::SCHEMA_TYPE:
				$entityMapping = $this->mapEntity(Entities\Widgets\Display\Slider::class);

				$display = $this->hydrateAttributes(
					Entities\Widgets\Display\Slider::class,
					$attributes,
					$entityMapping,
					null,
					null
				);

				$display['entity'] = Entities\Widgets\Display\Slider::class;
				$display[self::IDENTIFIER_KEY] = $identifier !== null && $identifier !== '' ? Uuid\Uuid::fromString($identifier) : $identifier;

				return $display;
		}

		return null;
	}

	/**
	 * @param JsonAPIDocument\Objects\IRelationshipObject $relationship
	 * @param JsonAPIDocument\Objects\IResourceObjectCollection|null $included
	 *
	 * @return mixed[]
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateDataSourcesRelationship(
		JsonAPIDocument\Objects\IRelationshipObject $relationship,
		?JsonAPIDocument\Objects\IResourceObjectCollection $included
	): array {
		if ($included === null) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.widgets.messages.missingDataSource.heading'),
				$this->translator->translate('//ui-module.widgets.messages.missingDataSource.message'),
				[
					'pointer' => '/data/relationships/data-sources/data/id',
				]
			);
		}

		$dataSources = [];

		foreach ($relationship->getIdentifiers() as $dataSourceRelationIdentifier) {
			if ($dataSourceRelationIdentifier->getType() === Schemas\Widgets\DataSources\ChannelPropertyDataSourceSchema::SCHEMA_TYPE) {
				foreach ($included->getAll() as $item) {
					if ($item->getId() === $dataSourceRelationIdentifier->getId()) {
						$dataSources[] = [
							'entity'   => Entities\Widgets\DataSources\ChannelPropertyDataSource::class,
							'channel'  => $item->getAttributes()->get('channel'),
							'property' => $item->getAttributes()->get('property'),
						];
					}
				}
			}
		}

		if ($dataSources === []) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.widgets.messages.missingDataSource.heading'),
				$this->translator->translate('//ui-module.widgets.messages.missingDataSource.message'),
				[
					'pointer' => '/data/relationships/data-sources/data/id',
				]
			);
		}

		return $dataSources;
	}

	/**
	 * @param JsonAPIDocument\Objects\IRelationshipObject $relationship
	 * @param JsonAPIDocument\Objects\IResourceObjectCollection|null $included
	 *
	 * @return mixed[]|null
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function hydrateGroupsRelationship(
		JsonAPIDocument\Objects\IRelationshipObject $relationship,
		?JsonAPIDocument\Objects\IResourceObjectCollection $included
	): ?array {
		if (!$relationship->isHasMany()) {
			return null;
		}

		$groups = [];

		foreach ($relationship->getIdentifiers() as $groupRelationIdentifier) {
			try {
				if ($groupRelationIdentifier->getId() !== null && $groupRelationIdentifier->getId() !== '') {
					$findQuery = new Queries\FindGroupsQuery();
					$findQuery->byId(Uuid\Uuid::fromString($groupRelationIdentifier->getId()));

					$group = $this->groupRepository->findOneBy($findQuery);

					if ($group !== null) {
						$groups[] = $group;
					}
				}
			} catch (Uuid\Exception\InvalidUuidStringException $ex) {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.invalidIdentifier.heading'),
					$this->translator->translate('//ui-module.base.messages.invalidIdentifier.message'),
					[
						'pointer' => '/data/relationships/groups/data/id',
					]
				);
			}
		}

		if ($groups === []) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.widgets.messages.missingGroups.heading'),
				$this->translator->translate('//ui-module.widgets.messages.missingGroups.message'),
				[
					'pointer' => '/data/relationships/groups/data/id',
				]
			);
		}

		return $groups;
	}

}
