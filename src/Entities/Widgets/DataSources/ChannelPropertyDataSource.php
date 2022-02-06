<?php declare(strict_types = 1);

/**
 * ChannelPropertyDataSource.php
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

use Doctrine\ORM\Mapping as ORM;
use FastyBird\UIModule\Entities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_ui_module_widgets_data_sources_channels_properties",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Widget data source connection to channel"
 *     }
 * )
 */
class ChannelPropertyDataSource extends DataSource implements IChannelPropertyDataSource
{

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="data_source_channel", length=100, nullable=false)
	 */
	private string $channel;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="data_source_property", length=100, nullable=false)
	 */
	private string $property;

	/**
	 * @param string $channel
	 * @param string $property
	 * @param Entities\Widgets\IWidget $widget
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		string $channel,
		string $property,
		Entities\Widgets\IWidget $widget,
		?Uuid\UuidInterface $id = null
	) {
		parent::__construct($widget, $id);

		$this->channel = $channel;
		$this->property = $property;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getChannel(): string
	{
		return $this->channel;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setChannel(string $channel): void
	{
		$this->channel = $channel;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getProperty(): string
	{
		return $this->property;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setProperty(string $property): void
	{
		$this->property = $property;
	}

}
