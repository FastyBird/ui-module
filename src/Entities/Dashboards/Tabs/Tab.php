<?php declare(strict_types = 1);

/**
 * Tab.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           03.08.24
 */

namespace FastyBird\Module\Ui\Entities\Dashboards\Tabs;

use DateTimeInterface;
use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\Library\Metadata\Types as MetadataTypes;
use FastyBird\Module\Ui\Entities;
use IPub\DoctrineCrud\Mapping\Attribute as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Nette\Utils;
use Ramsey\Uuid;
use function array_map;

#[ORM\Entity]
#[ORM\Table(
	name: 'fb_ui_module_tabs',
	options: [
		'collate' => 'utf8mb4_general_ci',
		'charset' => 'utf8mb4',
		'comment' => 'Widget tabs',
	],
)]
#[ORM\Index(columns: ['tab_name'], name: 'tab_name_idx')]
#[ORM\UniqueConstraint(name: 'tab_identifier_unique', columns: ['tab_identifier', 'dashboard_id'])]
class Tab implements Entities\Entity,
	Entities\EntityParams,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	use Entities\TEntity;
	use Entities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	#[ORM\Id]
	#[ORM\Column(name: 'tab_id', type: Uuid\Doctrine\UuidBinaryType::NAME)]
	#[ORM\CustomIdGenerator(class: Uuid\Doctrine\UuidGenerator::class)]
	private Uuid\UuidInterface $id;

	#[IPubDoctrine\Crud(required: true, writable: true)]
	#[ORM\Column(name: 'tab_identifier', type: 'string', nullable: false)]
	private string $identifier;

	#[IPubDoctrine\Crud(writable: true)]
	#[ORM\Column(name: 'tab_name', type: 'string', nullable: true, options: ['default' => null])]
	private string|null $name;

	#[IPubDoctrine\Crud(writable: true)]
	#[ORM\Column(name: 'tab_comment', type: 'text', nullable: true, options: ['default' => null])]
	private string|null $comment = null;

	#[IPubDoctrine\Crud(writable: true)]
	#[ORM\Column(name: 'tab_priority', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $priority = 0;

	/** @var Common\Collections\Collection<int, Entities\Widgets\Widget> */
	#[IPubDoctrine\Crud(writable: true)]
	#[ORM\ManyToMany(targetEntity: Entities\Widgets\Widget::class, inversedBy: 'tabs')]
	#[ORM\JoinTable(
		name: 'fb_ui_module_widgets_tabs',
		joinColumns: [
			new ORM\JoinColumn(
				name: 'tab_id',
				referencedColumnName: 'tab_id',
				onDelete: 'CASCADE',
			),
		],
		inverseJoinColumns: [
			new ORM\JoinColumn(
				name: 'widget_id',
				referencedColumnName: 'widget_id',
				onDelete: 'CASCADE',
			),
		],
	)]
	#[ORM\OrderBy(['id' => 'ASC'])]
	private Common\Collections\Collection $widgets;

	#[IPubDoctrine\Crud(required: true)]
	#[ORM\ManyToOne(
		targetEntity: Entities\Dashboards\Dashboard::class,
		cascade: ['persist'],
		inversedBy: 'tabs',
	)]
	#[ORM\JoinColumn(
		name: 'dashboard_id',
		referencedColumnName: 'dashboard_id',
		nullable: false,
		onDelete: 'CASCADE',
	)]
	protected Entities\Dashboards\Dashboard $dashboard;

	public function __construct(
		Entities\Dashboards\Dashboard $dashboard,
		string $identifier,
		Uuid\UuidInterface|null $id = null,
	)
	{
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->dashboard = $dashboard;

		$this->identifier = $identifier;

		$this->widgets = new Common\Collections\ArrayCollection();
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function getName(): string|null
	{
		return $this->name;
	}

	public function setName(string|null $name): void
	{
		$this->name = $name;
	}

	public function getComment(): string|null
	{
		return $this->comment;
	}

	public function setComment(string|null $comment = null): void
	{
		$this->comment = $comment;
	}

	public function getPriority(): int
	{
		return $this->priority;
	}

	public function setPriority(int $priority): void
	{
		$this->priority = $priority;
	}

	public function getDashboard(): Entities\Dashboards\Dashboard
	{
		return $this->dashboard;
	}

	public function addWidget(Entities\Widgets\Widget $widget): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->widgets->contains($widget)) {
			// ...and assign it to collection
			$this->widgets->add($widget);
		}
	}

	/**
	 * @return array<Entities\Widgets\Widget>
	 */
	public function getWidgets(): array
	{
		return $this->widgets->toArray();
	}

	/**
	 * @param array<Entities\Widgets\Widget> $widgets
	 */
	public function setWidgets(array $widgets = []): void
	{
		$this->widgets = new Common\Collections\ArrayCollection();

		foreach ($widgets as $entity) {
			$this->widgets->add($entity);
		}
	}

	public function getWidget(string $id): Entities\Widgets\Widget|null
	{
		$found = $this->widgets
			->filter(static fn (Entities\Widgets\Widget $row): bool => $id === $row->getId()->toString());

		return $found->isEmpty() ? null : $found->first();
	}

	public function removeWidget(Entities\Widgets\Widget $widget): void
	{
		// Check if collection contain removing entity...
		if ($this->widgets->contains($widget)) {
			// ...and remove it from collection
			$this->widgets->removeElement($widget);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'id' => $this->getId()->toString(),
			'identifier' => $this->getIdentifier(),
			'name' => $this->getName(),
			'comment' => $this->getComment(),
			'priority' => $this->getPriority(),

			'dashboard' => $this->getDashboard()->getId()->toString(),
			'widgets' => array_map(
				static fn (Entities\Widgets\Widget $widget): string => $widget->getId()->toString(),
				$this->getWidgets(),
			),

			'created_at' => $this->getCreatedAt()?->format(DateTimeInterface::ATOM),
			'updated_at' => $this->getUpdatedAt()?->format(DateTimeInterface::ATOM),
		];
	}

	public function getSource(): MetadataTypes\Sources\Source
	{
		return MetadataTypes\Sources\Module::UI;
	}

	/**
	 * @throws Utils\JsonException
	 */
	public function __toString(): string
	{
		return Utils\Json::encode($this->toArray());
	}

}
