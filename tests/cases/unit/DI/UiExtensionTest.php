<?php declare(strict_types = 1);

namespace FastyBird\Module\Ui\Tests\Cases\Unit\DI;

use Error;
use FastyBird\Library\Application\Exceptions as ApplicationExceptions;
use FastyBird\Module\Ui\Controllers;
use FastyBird\Module\Ui\Exceptions;
use FastyBird\Module\Ui\Hydrators;
use FastyBird\Module\Ui\Models;
use FastyBird\Module\Ui\Schemas;
use FastyBird\Module\Ui\Tests;
use Nette;
use RuntimeException;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class UiExtensionTest extends Tests\Cases\Unit\DbTestCase
{

	/**
	 * @throws ApplicationExceptions\InvalidArgument
	 * @throws Exceptions\InvalidArgument
	 * @throws Nette\DI\MissingServiceException
	 * @throws RuntimeException
	 * @throws Error
	 */
	public function testServicesRegistration(): void
	{
		self::assertNotNull($this->getContainer()->getByType(Models\Entities\Dashboards\Repository::class, false));
		self::assertNotNull($this->getContainer()->getByType(Models\Entities\Groups\Repository::class, false));
		self::assertNotNull($this->getContainer()->getByType(Models\Entities\Widgets\Repository::class, false));
		self::assertNotNull(
			$this->getContainer()->getByType(Models\Entities\Widgets\DataSources\Repository::class, false),
		);

		self::assertNotNull($this->getContainer()->getByType(Models\Entities\Dashboards\Manager::class, false));
		self::assertNotNull($this->getContainer()->getByType(Models\Entities\Groups\Manager::class, false));
		self::assertNotNull($this->getContainer()->getByType(Models\Entities\Widgets\Manager::class, false));
		self::assertNotNull($this->getContainer()->getByType(Models\Entities\Widgets\Displays\Manager::class, false));
		self::assertNotNull(
			$this->getContainer()->getByType(Models\Entities\Widgets\DataSources\Manager::class, false),
		);

		self::assertNotNull($this->getContainer()->getByType(Controllers\DashboardsV1::class, false));
		self::assertNotNull($this->getContainer()->getByType(Controllers\DataSourcesV1::class, false));
		self::assertNotNull($this->getContainer()->getByType(Controllers\DisplayV1::class, false));
		self::assertNotNull($this->getContainer()->getByType(Controllers\GroupsV1::class, false));
		self::assertNotNull($this->getContainer()->getByType(Controllers\WidgetsV1::class, false));

		self::assertNotNull($this->getContainer()->getByType(Schemas\Dashboards\Dashboard::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Groups\Group::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\AnalogActuator::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\AnalogSensor::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\DigitalActuator::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\DigitalSensor::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\Display\AnalogValue::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\Display\Button::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\Display\ChartGraph::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\Display\DigitalValue::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\Display\Gauge::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\Display\GroupedButton::class, false));
		self::assertNotNull($this->getContainer()->getByType(Schemas\Widgets\Display\Slider::class, false));
		self::assertNotNull(
			$this->getContainer()->getByType(Schemas\Widgets\DataSources\ChannelProperty::class, false),
		);

		self::assertNotNull($this->getContainer()->getByType(Hydrators\Dashboards\Dashboard::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Groups\Group::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\AnalogActuator::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\AnalogSensor::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\DigitalActuator::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\DigitalSensor::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\Displays\AnalogValue::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\Displays\Button::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\Displays\ChartGraph::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\Displays\DigitalValue::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\Displays\Gauge::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\Displays\GroupedButton::class, false));
		self::assertNotNull($this->getContainer()->getByType(Hydrators\Widgets\Displays\Slider::class, false));
		self::assertNotNull(
			$this->getContainer()->getByType(Hydrators\Widgets\DataSources\ChannelProperty::class, false),
		);
	}

}
