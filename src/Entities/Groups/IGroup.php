<?php declare(strict_types = 1);

/**
 * IGroup.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Entities\Groups;

use FastyBird\Database\Entities as DatabaseEntities;
use FastyBird\UIModule\Entities;
use IPub\DoctrineTimestampable;

/**
 * Dashboard group entity interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IGroup extends DatabaseEntities\IEntity,
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
	 * @param string|string $comment
	 *
	 * @return void
	 */
	public function setComment(?string $comment = null): void;

	/**
	 * @return string|null
	 */
	public function getComment(): ?string;

	/**
	 * @param int $priority
	 *
	 * @return void
	 */
	public function setPriority(int $priority): void;

	/**
	 * @return int
	 */
	public function getPriority(): int;

	/**
	 * @return Entities\Dashboards\IDashboard
	 */
	public function getDashboard(): Entities\Dashboards\IDashboard;

	/**
	 * @param Entities\Widgets\IWidget[] $widgets
	 *
	 * @return void
	 */
	public function setWidgets(array $widgets): void;

	/**
	 * @param Entities\Widgets\IWidget $widget
	 *
	 * @return void
	 */
	public function addWidget(Entities\Widgets\IWidget $widget): void;

	/**
	 * @return Entities\Widgets\IWidget[]
	 */
	public function getWidgets(): array;

	/**
	 * @param string $id
	 *
	 * @return Entities\Widgets\IWidget|null
	 */
	public function getWidget(string $id): ?Entities\Widgets\IWidget;

	/**
	 * @param Entities\Widgets\IWidget $widget
	 *
	 * @return void
	 */
	public function removeWidget(Entities\Widgets\IWidget $widget): void;

}
