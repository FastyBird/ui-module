<?php declare(strict_types = 1);

/**
 * TMinimumValue.php
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

use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;

/**
 * Display minimum value parameter
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method void setParam(string $key, $value = null)
 * @method mixed getParam(string $key, $default = null)
 */
trait TMinimumValue
{

	/**
	 * @var float|null
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 */
	protected ?float $minimumValue = null;

	/**
	 * @return float|null
	 */
	public function getMinimumValue(): ?float
	{
		$value = $this->getParam('minimumValue');

		return $value === null ? null : (float) $value;
	}

	/**
	 * @param float|null $minimumValue
	 *
	 * @return void
	 */
	public function setMinimumValue(?float $minimumValue): void
	{
		$this->minimumValue = $minimumValue;

		$this->setParam('minimumValue', $minimumValue);
	}

}
