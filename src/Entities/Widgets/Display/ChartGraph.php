<?php declare(strict_types = 1);

/**
 * ChartGraph.php
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
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;

/**
 * @ORM\Entity
 */
class ChartGraph extends Display implements IChartGraph
{

	use Entities\Widgets\Display\Parameters\TMinimumValue;
	use Entities\Widgets\Display\Parameters\TMaximumValue;
	use Entities\Widgets\Display\Parameters\TStepValue;
	use Entities\Widgets\Display\Parameters\TPrecision;

	/**
	 * @var bool
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 */
	protected $enableMinMax;

	/**
	 * {@inheritDoc}
	 */
	public function setEnableMinMax(bool $state): void
	{
		$this->enableMinMax = $state;

		$this->setParam('enableMinMax', $state);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isEnabledMinMax(): bool
	{
		return (bool) $this->getParam('enableMinMax', false);
	}

}
