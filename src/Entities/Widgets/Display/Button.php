<?php declare(strict_types = 1);

/**
 * Button.php
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

/**
 * @ORM\Entity
 */
class Button extends Display implements IButton
{

}
