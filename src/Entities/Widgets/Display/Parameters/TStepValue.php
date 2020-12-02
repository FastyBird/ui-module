<?php declare(strict_types = 1);

/**
 * TStepValue.php
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
 * Display step value parameter
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method void setParam(string $key, $value = null)
 * @method mixed getParam(string $key, $default = null)
 */
trait TStepValue
{

	/**
	 * @var float|null
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 */
	protected $stepValue;

	/**
	 * @param float|null $stepValue
	 *
	 * @return void
	 */
	public function setStepValue(?float $stepValue): void
	{
		$this->stepValue = $stepValue;

		$this->setParam('stepValue', $stepValue);
	}

	/**
	 * @return float
	 */
	public function getStepValue(): float
	{
		return (float) $this->getParam('stepValue', 0.1);
	}

}
