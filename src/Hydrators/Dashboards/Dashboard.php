<?php declare(strict_types = 1);

/**
 * Dashboard.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 * @since          1.0.0
 *
 * @date           26.05.20
 */

namespace FastyBird\Module\Ui\Hydrators\Dashboards;

use FastyBird\JsonApi\Hydrators as JsonApiHydrators;
use FastyBird\Module\Ui\Entities;
use FastyBird\Module\Ui\Hydrators;
use FastyBird\Module\Ui\Schemas;
use IPub\JsonAPIDocument;
use function is_scalar;

/**
 * Dashboard entity hydrator
 *
 * @extends JsonApiHydrators\Hydrator<Entities\Dashboards\Dashboard>
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Dashboard extends JsonApiHydrators\Hydrator
{

	/** @var array<int|string, string> */
	protected array $attributes = [
		'identifier',
		'name',
		'comment',
	];

	/** @var array<string> */
	protected array $relationships = [
		Schemas\Dashboards\Dashboard::RELATIONSHIPS_WIDGETS,
	];

	public function getEntityName(): string
	{
		return Entities\Dashboards\Dashboard::class;
	}

	protected function hydrateCommentAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): string|null
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
