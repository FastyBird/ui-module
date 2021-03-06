<?php declare(strict_types = 1);

/**
 * ChannelPropertyDataSourceSchema.php
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

namespace FastyBird\UIModule\Schemas\Widgets\DataSources;

use FastyBird\UIModule\Entities;
use Neomerx\JsonApi;

/**
 * Channel data source entity schema
 *
 * @package          FastyBird:UIModule!
 * @subpackage       Schemas
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends DataSourceSchema<Entities\Widgets\DataSources\IChannelPropertyDataSource>
 */
final class ChannelPropertyDataSourceSchema extends DataSourceSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'ui-module/widget-channel-data-source';

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Widgets\DataSources\ChannelPropertyDataSource::class;
	}

	/**
	 * @param Entities\Widgets\DataSources\IChannelPropertyDataSource $dataSource
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($dataSource, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return array_merge(
			(array) parent::getAttributes($dataSource, $context),
			[
				'channel'  => $dataSource->getChannel(),
				'property' => $dataSource->getProperty(),
			]
		);
	}

}
