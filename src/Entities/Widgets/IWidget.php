<?php declare(strict_types = 1);

/**
 * IWidget.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Entities\Widgets;

use FastyBird\Database\Entities as DatabaseEntities;
use FastyBird\UIModule\Entities;
use IPub\DoctrineTimestampable;

/**
 * Widget entity interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IWidget extends DatabaseEntities\IEntity,
	DatabaseEntities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName(string $name): void;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @param Entities\Widgets\Display\IDisplay $display
	 *
	 * @return void
	 */
	public function setDisplay(Entities\Widgets\Display\IDisplay $display): void;

	/**
	 * @return Entities\Widgets\Display\IDisplay
	 */
	public function getDisplay(): Entities\Widgets\Display\IDisplay;

	/**
	 * @param Entities\Widgets\DataSources\IDataSource[] $dataSources
	 *
	 * @return void
	 */
	public function setDataSources(array $dataSources = []): void;

	/**
	 * @param Entities\Widgets\DataSources\IDataSource $dataSource
	 *
	 * @return void
	 */
	public function addDataSource(Entities\Widgets\DataSources\IDataSource $dataSource): void;

	/**
	 * @return Entities\Widgets\DataSources\IDataSource[]
	 */
	public function getDataSources(): array;

	/**
	 * @param string $id
	 *
	 * @return Entities\Widgets\DataSources\IDataSource|null
	 */
	public function getDataSource(string $id): ?Entities\Widgets\DataSources\IDataSource;

	/**
	 * @param Entities\Widgets\DataSources\IDataSource $dataSource
	 *
	 * @return void
	 */
	public function removeDataSource(Entities\Widgets\DataSources\IDataSource $dataSource): void;

	/**
	 * @param Entities\Groups\IGroup[] $groups
	 *
	 * @return void
	 */
	public function setGroups(array $groups = []): void;

	/**
	 * @param Entities\Groups\IGroup $group
	 *
	 * @return void
	 */
	public function addGroup(Entities\Groups\IGroup $group): void;

	/**
	 * @return Entities\Groups\IGroup[]
	 */
	public function getGroups(): array;

	/**
	 * @param string $id
	 *
	 * @return Entities\Groups\IGroup|null
	 */
	public function getGroup(string $id): ?Entities\Groups\IGroup;

	/**
	 * @param Entities\Groups\IGroup $group
	 *
	 * @return void
	 */
	public function removeGroup(Entities\Groups\IGroup $group): void;

	/**
	 * @return string
	 */
	public function getType(): string;

}
