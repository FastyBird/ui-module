<?php declare(strict_types = 1);

/**
 * ChannelPropertyDataSourceHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           27.05.20
 */

namespace FastyBird\UIModule\Hydrators\Widgets\DataSources;

use FastyBird\UIModule\Entities;

/**
 * Channel data source entity hydrator
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelPropertyDataSourceHydrator extends DataSourceHydrator
{

	/** @var string[] */
	protected array $attributes = [
		'channel',
		'property',
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Widgets\DataSources\ChannelPropertyDataSource::class;
	}

}
