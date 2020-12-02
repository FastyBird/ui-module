<?php declare(strict_types = 1);

/**
 * AnalogValue.php
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

/**
 * @ORM\Entity
 */
class AnalogValue extends Display implements IAnalogValue
{

	use Entities\Widgets\Display\Parameters\TPrecision;

}
