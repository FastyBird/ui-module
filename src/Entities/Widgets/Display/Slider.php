<?php declare(strict_types = 1);

/**
 * Slider.php
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

namespace FastyBird\UIModule\Entities\Widgets\Display;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\UIModule\Entities;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 */
class Slider extends Display implements ISlider
{

	use Entities\Widgets\Display\Parameters\TMinimumValue;
	use Entities\Widgets\Display\Parameters\TMaximumValue;
	use Entities\Widgets\Display\Parameters\TStepValue;
	use Entities\Widgets\Display\Parameters\TPrecision;

	/**
	 * @param Entities\Widgets\IWidget $widget
	 * @param float $minimumValue
	 * @param float $maximumValue
	 * @param float $stepValue
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Widgets\IWidget $widget,
		float $minimumValue,
		float $maximumValue,
		float $stepValue,
		?Uuid\UuidInterface $id = null
	) {
		parent::__construct($widget, $id);

		$this->setMinimumValue($minimumValue);
		$this->setMaximumValue($maximumValue);
		$this->setStepValue($stepValue);
	}

}
