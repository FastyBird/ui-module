<?php declare(strict_types = 1);

/**
 * AnalogSensor.php
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

use Doctrine\ORM\Mapping as ORM;
use FastyBird\UIModule\Entities;

/**
 * @ORM\Entity
 */
class AnalogSensor extends Sensor implements IAnalogSensor
{

	/** @var string[] */
	protected $allowedDisplay = [
		Entities\Widgets\Display\IChartGraph::class,
		Entities\Widgets\Display\IGauge::class,
		Entities\Widgets\Display\IAnalogValue::class,
	];

}
