<?php declare(strict_types = 1);

/**
 * TIcon.php
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
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;

/**
 * Display icon parameter
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method void setParam(string $key, $value = null)
 * @method mixed getParam(string $key, $default = null)
 */
trait TIcon
{

	/**
	 * @var Types\WidgetIconType|null
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 */
	protected ?Types\WidgetIconType $icon;

	/**
	 * @return Types\WidgetIconType|null
	 */
	public function getIcon(): ?Types\WidgetIconType
	{
		$value = $this->getParam('icon');

		return $value === null ? null : Types\WidgetIconType::get($value);
	}

	/**
	 * @param Types\WidgetIconType|null $icon
	 *
	 * @return void
	 */
	public function setIcon(?Types\WidgetIconType $icon): void
	{
		$this->icon = $icon;

		if ($icon !== null) {
			$this->setParam('icon', $icon->getValue());

		} else {
			$this->setParam('icon', null);
		}
	}

}
