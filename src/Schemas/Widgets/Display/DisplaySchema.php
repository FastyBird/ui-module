<?php declare(strict_types = 1);

/**
 * DisplaySchema.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           26.05.20
 */

namespace FastyBird\UIModule\Schemas\Widgets\Display;

use FastyBird\JsonApi\Schemas as JsonApiSchemas;
use FastyBird\UIModule;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Router;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Widget display entity schema constructor
 *
 * @package          FastyBird:UIModule!
 * @subpackage       Schemas
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template  TEntityClass of Entities\Widgets\Display\IDisplay
 * @phpstan-extends   JsonApiSchemas\JsonApiSchema<TEntityClass>
 */
abstract class DisplaySchema extends JsonApiSchemas\JsonApiSchema
{

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_WIDGET = 'widget';

	/** @var Routing\IRouter */
	protected Routing\IRouter $router;

	/**
	 * @param Routing\IRouter $router
	 */
	public function __construct(
		Routing\IRouter $router
	) {
		$this->router = $router;
	}

	/**
	 * @param Entities\Widgets\Display\IDisplay $display
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpstan-param TEntityClass $display
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($display, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [];
	}

	/**
	 * @param Entities\Widgets\Display\IDisplay $display
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param TEntityClass $display
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($display): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				UIModule\Constants::ROUTE_NAME_WIDGET_DISPLAY,
				[
					Router\Routes::URL_ITEM_ID   => $display->getPlainId(),
					Router\Routes::URL_WIDGET_ID => $display->getWidget()->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Widgets\Display\IDisplay $display
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpstan-param TEntityClass $display
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($display, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_WIDGET => [
				self::RELATIONSHIP_DATA          => $display->getWidget(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];
	}

	/**
	 * @param Entities\Widgets\Display\IDisplay $display
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param TEntityClass $display
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($display, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_WIDGET) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					UIModule\Constants::ROUTE_NAME_WIDGET,
					[
						Router\Routes::URL_ITEM_ID => $display->getWidget()->getPlainId(),
					]
				),
				false
			);
		}

		return parent::getRelationshipRelatedLink($display, $name);
	}

	/**
	 * @param Entities\Widgets\Display\IDisplay $display
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param TEntityClass $display
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($display, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_WIDGET) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					UIModule\Constants::ROUTE_NAME_WIDGET_DISPLAY_RELATIONSHIP,
					[
						Router\Routes::URL_ITEM_ID     => $display->getPlainId(),
						Router\Routes::URL_WIDGET_ID   => $display->getWidget()->getPlainId(),
						Router\Routes::RELATION_ENTITY => $name,
					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($display, $name);
	}

}
