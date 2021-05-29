<?php declare(strict_types = 1);

/**
 * IDashboard.php
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

namespace FastyBird\UIModule\Entities\Dashboards;

use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\UIModule\Entities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_dashboards",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="User interface widgets dashboards"
 *     },
 *     indexes={
 *       @ORM\Index(name="dashboard_name_idx", columns={"dashboard_name"})
 *     }
 * )
 */
class Dashboard implements IDashboard
{

	use Entities\TEntity;
	use Entities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="dashboard_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected Uuid\UuidInterface $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="dashboard_name", length=50, nullable=false)
	 */
	private string $name;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="text", name="dashboard_comment", nullable=true, options={"default": null})
	 */
	private ?string $comment = null;

	/**
	 * @var int
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="integer", name="dashboard_priority", length=15, nullable=false, options={"default" = 0})
	 */
	private int $priority = 0;

	/**
	 * @var Common\Collections\Collection<int, Entities\Groups\IGroup>
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 * @ORM\OneToMany(targetEntity="FastyBird\UIModule\Entities\Groups\Group", mappedBy="dashboard", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @ORM\OrderBy({"priority" = "ASC"})
	 */
	private Common\Collections\Collection $groups;

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
	public function addGroup(Entities\Groups\IGroup $group): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->groups->contains($group)) {
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
	public function setGroups(array $groups = []): void
	{
		$this->groups = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Groups\IGroup $entity */
		foreach ($groups as $entity) {
			if (!$this->groups->contains($entity)) {
				// ...and assign them to collection
				$this->groups->add($entity);
			}
		}
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

		return $found->isEmpty() ? null : $found->first();
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
