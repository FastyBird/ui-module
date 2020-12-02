<?php declare(strict_types = 1);

/**
 * DataSources.php
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

namespace FastyBird\UIModule\Entities\Widgets\DataSources;

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
 *     name="fb_widgets_data_sources",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="User interface widgets data sources"
 *     },
 *     indexes={
 *       @ORM\Index(name="data_source_type_idx", columns={"data_source_type"})
 *     }
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="data_source_type", type="string", length=25)
 * @ORM\DiscriminatorMap({
 *    "channel_property" = "FastyBird\UIModule\Entities\Widgets\DataSources\ChannelPropertyDataSource"
 * })
 * @ORM\MappedSuperclass
 */
abstract class DataSource implements IDataSource
{

	use DatabaseEntities\TEntity;
	use DatabaseEntities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="data_source_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var Entities\Widgets\IWidget
	 *
	 * @IPubDoctrine\Crud(is="required")
	 * @ORM\ManyToOne(targetEntity="FastyBird\UIModule\Entities\Widgets\Widget", inversedBy="dataSources")
	 * @ORM\JoinColumn(name="widget_id", referencedColumnName="widget_id", onDelete="CASCADE")
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

		$widget->addDataSource($this);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getWidget(): Entities\Widgets\IWidget
	{
		return $this->widget;
	}

}
