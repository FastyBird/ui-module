<?php declare(strict_types = 1);

/**
 * Widget.php
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

namespace FastyBird\UIModule\Entities\Widgets;

use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\Database\Entities as DatabaseEntities;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Exceptions;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_widgets",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="User interface widgets"
 *     },
 *     indexes={
 *       @ORM\Index(name="widget_type_idx", columns={"widget_type"})
 *     }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="widget_type", type="string", length=20)
 * @ORM\DiscriminatorMap({
 *    "analog_actuator"    = "FastyBird\UIModule\Entities\Widgets\AnalogActuator",
 *    "digital_actuator"   = "FastyBird\UIModule\Entities\Widgets\DigitalActuator",
 *    "analog_sensor"      = "FastyBird\UIModule\Entities\Widgets\AnalogSensor",
 *    "digital_sensor"     = "FastyBird\UIModule\Entities\Widgets\DigitalSensor"
 * })
 * @ORM\MappedSuperclass
 *
 * @property-read string[] $allowedDisplay
 */
abstract class Widget implements IWidget
{

	use DatabaseEntities\TEntity;
	use DatabaseEntities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="widget_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="widget_name", length=50, nullable=false)
	 */
	protected $name;

	/**
	 * @var Entities\Widgets\Display\IDisplay
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\OneToOne(targetEntity="FastyBird\UIModule\Entities\Widgets\Display\Display", mappedBy="widget", cascade={"persist", "remove"})
	 */
	protected $display;

	/**
	 * @var Common\Collections\Collection<int, Entities\Groups\IGroup>
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 * @ORM\ManyToMany(targetEntity="FastyBird\UIModule\Entities\Groups\Group", mappedBy="widgets")
	 */
	protected $groups;

	/**
	 * @var Common\Collections\Collection<int, Entities\Widgets\DataSources\IDataSource>
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 * @ORM\OneToMany(targetEntity="FastyBird\UIModule\Entities\Widgets\DataSources\DataSource", mappedBy="widget", cascade={"persist", "remove"},
	 *                                                                                         orphanRemoval=true)
	 */
	protected $dataSources;

	/**
	 * @param string $name
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		string $name,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->name = $name;

		$this->groups = new Common\Collections\ArrayCollection();
		$this->dataSources = new Common\Collections\ArrayCollection();
	}

	/**
	 * {@inheritDoc
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * {@inheritDoc
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDisplay(Entities\Widgets\Display\IDisplay $display): void
	{
		$isAllowed = false;

		foreach ($this->allowedDisplay as $allowedClass) {
			if ($display instanceof $allowedClass) {
				$isAllowed = true;
			}
		}

		if (!$isAllowed) {
			throw new Exceptions\InvalidArgumentException('Provided display entity is not valid for this widget type');
		}

		$this->display = $display;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDisplay(): Entities\Widgets\Display\IDisplay
	{
		return $this->display;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDataSources(array $dataSources = []): void
	{
		$this->dataSources = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Widgets\DataSources\IDataSource $entity */
		foreach ($dataSources as $entity) {
			if (!$this->dataSources->contains($entity)) {
				// ...and assign them to collection
				$this->dataSources->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addDataSource(Entities\Widgets\DataSources\IDataSource $dataSource): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->dataSources->contains($dataSource)) {
			// ...and assign it to collection
			$this->dataSources->add($dataSource);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDataSources(): array
	{
		return $this->dataSources->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDataSource(string $id): ?Entities\Widgets\DataSources\IDataSource
	{
		$found = $this->dataSources
			->filter(function (Entities\Widgets\DataSources\IDataSource $row) use ($id): bool {
				if ($row instanceof Entities\Widgets\DataSources\IChannelPropertyDataSource) {
					return $id === $row->getChannel();
				}

				return false;
			});

		return $found->isEmpty() || $found->first() === false ? null : $found->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeDataSource(Entities\Widgets\DataSources\IDataSource $dataSource): void
	{
		// Check if collection contain removing entity...
		if ($this->dataSources->contains($dataSource)) {
			// ...and remove it from collection
			$this->dataSources->removeElement($dataSource);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function setGroups(array $groups = []): void
	{
		$this->groups = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Groups\IGroup $entity */
		foreach ($groups as $entity) {
			if (!$this->groups->contains($entity)) {
				$entity->addWidget($this);

				// ...and assign them to collection
				$this->groups->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addGroup(Entities\Groups\IGroup $group): void
	{
		$this->groups = new Common\Collections\ArrayCollection();

		// Check if collection does not contain inserting entity
		if (!is_array($this->groups) && !$this->groups->contains($group)) {
			$group->addWidget($this);

			// ...and assign it to collection
			$this->groups->add($group);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGroups(): array
	{
		return $this->groups->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGroup(string $id): ?Entities\Groups\IGroup
	{
		$found = $this->groups
			->filter(function (Entities\Groups\IGroup $row) use ($id): bool {
				return $id === $row->getPlainId();
			});

		return $found->isEmpty() || $found->first() === false ? null : $found->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeGroup(Entities\Groups\IGroup $group): void
	{
		// Check if collection contain removing entity...
		if ($this->groups->contains($group)) {
			// ...and remove it from collection
			$this->groups->removeElement($group);
		}
	}

}
