<?php declare(strict_types = 1);

/**
 * WidgetSchema.php
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

namespace FastyBird\UIModule\Schemas\Widgets;

use FastyBird\JsonApi\Schemas as JsonApiSchemas;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Router;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Widget entity schema
 *
 * @package          FastyBird:UIModule!
 * @subpackage       Schemas
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Widgets\IWidget
 * @phpstan-extends  JsonApiSchemas\JsonApiSchema<T>
 */
abstract class WidgetSchema extends JsonApiSchemas\JsonApiSchema
{

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_DISPLAY = 'display';
	public const RELATIONSHIPS_GROUPS = 'groups';
	public const RELATIONSHIPS_DATA_SOURCES = 'data-sources';

	/** @var Routing\IRouter */
	protected $router;

	/**
	 * @param Routing\IRouter $router
	 */
	public function __construct(
		Routing\IRouter $router
	) {
		$this->router = $router;
	}

	/**
	 * @param Entities\Widgets\IWidget $widget
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpstan-param T $widget
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($widget, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'name' => $widget->getName(),

			'params' => (array) $widget->getParams(),
		];
	}

	/**
	 * @param Entities\Widgets\IWidget $widget
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $widget
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($widget): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				'widget',
				[
					Router\Routes::URL_ITEM_ID => $widget->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Widgets\IWidget $widget
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpstan-param T $widget
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($widget, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_DISPLAY      => [
				self::RELATIONSHIP_DATA          => $widget->getDisplay(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_DATA_SOURCES => [
				self::RELATIONSHIP_DATA          => $widget->getDataSources(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_GROUPS       => [
				self::RELATIONSHIP_DATA          => $widget->getGroups(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => false,
			],
		];
	}

	/**
	 * @param Entities\Widgets\IWidget $widget
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $widget
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($widget, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_DISPLAY) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'widget.display',
					[
						Router\Routes::URL_WIDGET_ID => $widget->getPlainId(),
					]
				),
				false
			);

		} elseif ($name === self::RELATIONSHIPS_DATA_SOURCES) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'widget.data-sources',
					[
						Router\Routes::URL_WIDGET_ID => $widget->getPlainId(),
					]
				),
				true,
				[
					'count' => count($widget->getDataSources()),
				]
			);
		}

		return parent::getRelationshipRelatedLink($widget, $name);
	}

	/**
	 * @param Entities\Widgets\IWidget $widget
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $widget
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($widget, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if (
			$name === self::RELATIONSHIPS_DISPLAY
			|| $name === self::RELATIONSHIPS_DATA_SOURCES
			|| $name === self::RELATIONSHIPS_GROUPS
		) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'widget.relationship',
					[
						Router\Routes::URL_ITEM_ID     => $widget->getPlainId(),
						Router\Routes::RELATION_ENTITY => $name,
					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($widget, $name);
	}

}
