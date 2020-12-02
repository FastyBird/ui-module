<?php declare(strict_types = 1);

/**
 * DigitalSensor.php
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

use Doctrine\ORM\Mapping as ORM;
use FastyBird\UIModule\Entities;

/**
 * @ORM\Entity
 */
class DigitalSensor extends Sensor implements IDigitalSensor
{

	/** @var string[] */
	protected $allowedDisplay = [
		Entities\Widgets\Display\IDigitalValue::class,
		Entities\Widgets\Display\IChartGraph::class,
	];

}
