<?php declare(strict_types = 1);

/**
 * IDataSource.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Entities\Widgets\DataSources;

use FastyBird\Database\Entities as DatabaseEntities;
use FastyBird\UIModule\Entities;
use IPub\DoctrineTimestampable;

/**
 * Widget data source entity interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IDataSource extends DatabaseEntities\IEntity,
	DatabaseEntities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @return Entities\Widgets\IWidget
	 */
	public function getWidget(): Entities\Widgets\IWidget;

}
