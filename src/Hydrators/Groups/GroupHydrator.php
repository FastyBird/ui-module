<?php declare(strict_types = 1);

/**
 * GroupHydrator.php
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

namespace FastyBird\UIModule\Hydrators\Groups;

use FastyBird\JsonApi\Hydrators as JsonApiHydrators;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Schemas;
use IPub\JsonAPIDocument;

/**
 * Dashboard group entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends JsonApiHydrators\Hydrator<Entities\Groups\IGroup>
 */
final class GroupHydrator extends JsonApiHydrators\Hydrator
{

	/** @var string[] */
	protected array $attributes = [
		'name',
		'comment',
	];

	/** @var string[] */
	protected array $relationships = [
		Schemas\Groups\GroupSchema::RELATIONSHIPS_DASHBOARD,
	];

	/**
	 * {@inheritDoc}
	 */
	public function getEntityName(): string
	{
		return Entities\Groups\Group::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateCommentAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if (
			!is_scalar($attributes->get('comment'))
			|| (string) $attributes->get('comment') === ''
		) {
			return null;
		}

		return (string) $attributes->get('comment');
	}

}
