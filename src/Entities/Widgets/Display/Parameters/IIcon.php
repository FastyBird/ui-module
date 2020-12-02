<?php declare(strict_types = 1);

/**
 * IIcon.php
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

namespace FastyBird\UIModule\Entities\Widgets\Display\Parameters;

use FastyBird\UIModule\Types;

/**
 * Display icon parameter interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IIcon
{

	/**
	 * @param Types\WidgetIconType $icon
	 *
	 * @return void
	 */
	public function setIcon(Types\WidgetIconType $icon): void;

	/**
	 * @return Types\WidgetIconType|null
	 */
	public function getIcon(): ?Types\WidgetIconType;

}
