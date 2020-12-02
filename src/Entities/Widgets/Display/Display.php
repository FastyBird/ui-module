<?php declare(strict_types = 1);

/**
 * Display.php
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

namespace FastyBird\UIModule\Entities\Widgets\Display;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\Database\Entities as DatabaseEntities;
use FastyBird\UIModule\Entities;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_widgets_display",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="User interface widgets display settings"
 *     },
 *     indexes={
 *       @ORM\Index(name="display_type_idx", columns={"display_type"})
 *     }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="display_type", type="string", length=15)
 * @ORM\DiscriminatorMap({
 *    "button"           = "FastyBird\UIModule\Entities\Widgets\Display\Button",
 *    "grouped_button"   = "FastyBird\UIModule\Entities\Widgets\Display\GroupedButton",
 *    "slider"           = "FastyBird\UIModule\Entities\Widgets\Display\Slider",
 *    "gauge"            = "FastyBird\UIModule\Entities\Widgets\Display\Gauge",
 *    "chart_graph"      = "FastyBird\UIModule\Entities\Widgets\Display\ChartGraph",
 *    "analog_value"     = "FastyBird\UIModule\Entities\Widgets\Display\AnalogValue",
 *    "digital_value"    = "FastyBird\UIModule\Entities\Widgets\Display\DigitalValue"
 * })
 * @ORM\MappedSuperclass
 */
abstract class Display implements IDisplay
{

	use DatabaseEntities\TEntity;
	use DatabaseEntities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="display_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var Entities\Widgets\IWidget
	 *
	 * @ORM\OneToOne(targetEntity="FastyBird\UIModule\Entities\Widgets\Widget", inversedBy="display")
	 * @ORM\JoinColumn(name="widget_id", referencedColumnName="widget_id", unique=true, onDelete="CASCADE", nullable=false)
	 */
	protected $widget;

	/**
	 * @param Entities\Widgets\IWidget $widget
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Widgets\IWidget $widget,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->widget = $widget;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getWidget(): Entities\Widgets\IWidget
	{
		return $this->widget;
	}

}
