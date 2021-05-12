<?php declare(strict_types = 1);

/**
 * Group.php
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

namespace FastyBird\UIModule\Entities\Groups;

use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\Database\Entities as DatabaseEntities;
use FastyBird\UIModule\Entities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_dashboards_groups",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Dashboard groups"
 *     }
 * )
 */
class Group implements IGroup
{

	use DatabaseEntities\TEntity;
	use DatabaseEntities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="group_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected Uuid\UuidInterface $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="group_name", length=50, nullable=false)
	 */
	private string $name;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="text", name="group_comment", nullable=true, options={"default": null})
	 */
	private ?string $comment = null;

	/**
	 * @var int
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="integer", name="group_priority", length=15, nullable=false, options={"default" = 0})
	 */
	private int $priority = 0;

	/**
	 * @var Common\Collections\Collection<int, Entities\Widgets\IWidget>
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 * @ORM\ManyToMany(targetEntity="FastyBird\UIModule\Entities\Widgets\Widget", inversedBy="groups")
	 * @ORM\JoinTable(name="fb_widgets_groups",
	 *    joinColumns={
	 *       @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
	 *    },
	 *    inverseJoinColumns={
	 *       @ORM\JoinColumn(name="widget_id", referencedColumnName="widget_id", onDelete="CASCADE")
	 *    }
	 * )
	 */
	private Common\Collections\Collection $widgets;

	/**
	 * @var Entities\Dashboards\IDashboard
	 *
	 * @IPubDoctrine\Crud(is="required")
	 * @ORM\ManyToOne(targetEntity="FastyBird\UIModule\Entities\Dashboards\Dashboard", inversedBy="groups")
	 * @ORM\JoinColumn(name="dashboard_id", referencedColumnName="dashboard_id", onDelete="CASCADE", nullable=false)
	 */
	private Entities\Dashboards\IDashboard $dashboard;

	/**
	 * @param Entities\Dashboards\IDashboard $dashboard
	 * @param string $name
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Dashboards\IDashboard $dashboard,
		string $name,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->dashboard = $dashboard;

		$this->name = $name;

		$this->widgets = new Common\Collections\ArrayCollection();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getComment(): ?string
	{
		return $this->comment;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setComment(?string $comment = null): void
	{
		$this->comment = $comment;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPriority(): int
	{
		return $this->priority;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPriority(int $priority): void
	{
		$this->priority = $priority;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDashboard(): Entities\Dashboards\IDashboard
	{
		return $this->dashboard;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addWidget(Entities\Widgets\IWidget $widget): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->widgets->contains($widget)) {
			// ...and assign it to collection
			$this->widgets->add($widget);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getWidgets(): array
	{
		return $this->widgets->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function setWidgets(array $widgets = []): void
	{
		$this->widgets = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Widgets\IWidget $entity */
		foreach ($widgets as $entity) {
			if (!$this->widgets->contains($entity)) {
				// ...and assign them to collection
				$this->widgets->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getWidget(string $id): ?Entities\Widgets\IWidget
	{
		$found = $this->widgets
			->filter(function (Entities\Widgets\IWidget $row) use ($id): bool {
				return $id === $row->getPlainId();
			});

		return $found->isEmpty() ? null : $found->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeWidget(Entities\Widgets\IWidget $widget): void
	{
		// Check if collection contain removing entity...
		if ($this->widgets->contains($widget)) {
			// ...and remove it from collection
			$this->widgets->removeElement($widget);
		}
	}

}
