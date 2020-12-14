<?php declare(strict_types = 1);

/**
 * DashboardSchema.php
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

namespace FastyBird\UIModule\Schemas\Dashboards;

use FastyBird\JsonApi\Schemas as JsonApiSchemas;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Router;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Dashboard entity schema
 *
 * @package          FastyBird:UIModule!
 * @subpackage       Schemas
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Dashboards\IDashboard
 * @phpstan-extends  JsonApiSchemas\JsonApiSchema<T>
 */
final class DashboardSchema extends JsonApiSchemas\JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'ui-module/dashboard';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_GROUPS = 'groups';

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
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Dashboards\Dashboard::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Dashboards\IDashboard $dashboard
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($dashboard, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'name'     => $dashboard->getName(),
			'comment'  => $dashboard->getComment(),
			'priority' => $dashboard->getPriority(),

			'params' => (array) $dashboard->getParams(),
		];
	}

	/**
	 * @param Entities\Dashboards\IDashboard $dashboard
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($dashboard): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				'dashboard',
				[
					Router\Routes::URL_ITEM_ID => $dashboard->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Dashboards\IDashboard $dashboard
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($dashboard, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_GROUPS => [
				self::RELATIONSHIP_DATA          => $dashboard->getGroups(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];
	}

	/**
	 * @param Entities\Dashboards\IDashboard $dashboard
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($dashboard, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_GROUPS) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'dashboard.groups',
					[
						Router\Routes::URL_DASHBOARD_ID => $dashboard->getPlainId(),
					]
				),
				true,
				[
					'count' => count($dashboard->getGroups()),
				]
			);
		}

		return parent::getRelationshipRelatedLink($dashboard, $name);
	}

	/**
	 * @param Entities\Dashboards\IDashboard $dashboard
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $dashboard
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($dashboard, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_GROUPS) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'dashboard.relationship',
					[
						Router\Routes::URL_ITEM_ID     => $dashboard->getPlainId(),
						Router\Routes::RELATION_ENTITY => $name,
					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($dashboard, $name);
	}

}
