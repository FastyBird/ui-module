<?php declare(strict_types = 1);

/**
 * DashboardHydrator.php
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

namespace FastyBird\UIModule\Hydrators\Dashboards;

use FastyBird\JsonApi\Hydrators as JsonApiHydrators;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Hydrators;
use IPub\JsonAPIDocument;

/**
 * Dashboard entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DashboardHydrator extends JsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'name',
		'comment',
	];

	/** @var string */
	protected $translationDomain = 'ui-module.dashboards';

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Dashboards\Dashboard::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateCommentAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if ($attributes->get('comment') === null || (string) $attributes->get('comment') === '') {
			return null;
		}

		return (string) $attributes->get('comment');
	}

}
