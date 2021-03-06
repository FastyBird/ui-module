<?php declare(strict_types = 1);

/**
 * DigitalActuator.php
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

namespace FastyBird\UIModule\Entities\Widgets;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\UIModule\Entities;

/**
 * @ORM\Entity
 */
class DigitalActuator extends Actuator implements IDigitalActuator
{

	/** @var string[] */
	protected array $allowedDisplay = [
		Entities\Widgets\Display\IButton::class,
	];

}
