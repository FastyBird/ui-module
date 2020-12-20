<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\UIModule\Consumers;
use FastyBird\UIModule\Controllers;
use FastyBird\UIModule\DI;
use FastyBird\UIModule\Events;
use FastyBird\UIModule\Hydrators;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Schemas;
use FastyBird\UIModule\Sockets;
use Nette;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class ServicesTest extends BaseTestCase
{

	public function testServicesRegistration(): void
	{
		$container = $this->createContainer();

		Assert::notNull($container->getByType(Events\WsClientConnectedHandler::class));
		Assert::notNull($container->getByType(Events\WsMessageHandler::class));
		Assert::notNull($container->getByType(Events\ServerSocketConnectHandler::class));

		Assert::notNull($container->getByType(Models\Dashboards\DashboardRepository::class));
		Assert::notNull($container->getByType(Models\Groups\GroupRepository::class));
		Assert::notNull($container->getByType(Models\Widgets\WidgetRepository::class));
		Assert::notNull($container->getByType(Models\Widgets\DataSources\DataSourceRepository::class));

		Assert::notNull($container->getByType(Models\Dashboards\DashboardsManager::class));
		Assert::notNull($container->getByType(Models\Groups\GroupsManager::class));
		Assert::notNull($container->getByType(Models\Widgets\WidgetsManager::class));
		Assert::notNull($container->getByType(Models\Widgets\Displays\DisplaysManager::class));
		Assert::notNull($container->getByType(Models\Widgets\DataSources\DataSourcesManager::class));

		Assert::notNull($container->getByType(Controllers\DashboardsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\GroupsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\WidgetsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\DisplayV1Controller::class));
		Assert::notNull($container->getByType(Controllers\DataSourcesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\ExchangeController::class));

		Assert::notNull($container->getByType(Schemas\Dashboards\DashboardSchema::class));
		Assert::notNull($container->getByType(Schemas\Groups\GroupSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\AnalogActuatorSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\AnalogSensorSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\DigitalActuatorSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\DigitalSensorSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\Display\AnalogValueSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\Display\ButtonSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\Display\ChartGraphSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\Display\DigitalValueSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\Display\GaugeSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\Display\GroupedButtonSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\Display\SliderSchema::class));
		Assert::notNull($container->getByType(Schemas\Widgets\DataSources\ChannelPropertyDataSourceSchema::class));

		Assert::notNull($container->getByType(Hydrators\Dashboards\DashboardHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Groups\GroupHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\AnalogActuatorWidgetHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\AnalogSensorWidgetHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\DigitalActuatorWidgetHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\DigitalSensorWidgetHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\Displays\AnalogValueHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\Displays\ButtonHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\Displays\ChartGraphHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\Displays\DigitalValueHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\Displays\GaugeHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\Displays\GroupedButtonHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\Displays\SliderHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Widgets\DataSources\ChannelPropertyDataSourceHydrator::class));

		Assert::notNull($container->getByType(Consumers\ModuleMessageConsumer::class));

		Assert::notNull($container->getByType(Sockets\Sender::class));
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer(): Nette\DI\Container
	{
		$rootDir = __DIR__ . '/../../../';

		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addParameters(['container' => ['class' => 'SystemContainer_' . md5((string) time())]]);
		$config->addParameters(['appDir' => $rootDir, 'wwwDir' => $rootDir]);

		$config->addConfig(__DIR__ . '/../../../common.neon');

		DI\UIModuleExtension::register($config);

		return $config->createContainer();
	}

}

$test_case = new ServicesTest();
$test_case->run();
