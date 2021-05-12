<?php declare(strict_types = 1);

/**
 * GroupedButton.php
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

namespace FastyBird\UIModule\Entities\Widgets\Display;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\UIModule\Entities;

/**
 * @ORM\Entity
 */
class GroupedButton extends Display implements IGroupedButton
{

	use Entities\Widgets\Display\Parameters\TIcon;

}
