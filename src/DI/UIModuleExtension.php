<?php declare(strict_types = 1);

/**
 * UIModuleExtension.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     DI
 * @since          0.1.0
 *
 * @date           02.12.20
 */

namespace FastyBird\UIModule\DI;

use Contributte\Translation;
use Doctrine\Persistence;
use FastyBird\UIModule\Commands;
use FastyBird\UIModule\Controllers;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Hydrators;
use FastyBird\UIModule\Middleware;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Router;
use FastyBird\UIModule\Schemas;
use IPub\DoctrineCrud;
use IPub\SlimRouter\Routing as SlimRouterRouting;
use Nette;
use Nette\DI;
use Nette\PhpGenerator;
use Nette\Schema;
use stdClass;

/**
 * UI module extension container
 *
 * @package        FastyBird:UIModule!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class UIModuleExtension extends DI\CompilerExtension implements Translation\DI\TranslationProviderInterface
{

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(
		Nette\Configurator $config,
		string $extensionName = 'fbUiModule'
	): void {
		$config->onCompile[] = function (
			Nette\Configurator $config,
			DI\Compiler $compiler
		) use ($extensionName): void {
			$compiler->addExtension($extensionName, new UIModuleExtension());
		};
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfigSchema(): Schema\Schema
	{
		return Schema\Expect::structure([
			'apiPrefix' => Schema\Expect::bool(false),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $configuration */
		$configuration = $this->getConfig();

		// Http router
		$builder->addDefinition($this->prefix('middleware.access'), new DI\Definitions\ServiceDefinition())
			->setType(Middleware\AccessMiddleware::class);

		$builder->addDefinition($this->prefix('router.routes'), new DI\Definitions\ServiceDefinition())
			->setType(Router\Routes::class)
			->setArguments(['usePrefix' => $configuration->apiPrefix]);

		$builder->addDefinition($this->prefix('router.validator'), new DI\Definitions\ServiceDefinition())
			->setType(Router\Validator::class);

		// Console commands
		$builder->addDefinition($this->prefix('commands.initialize'), new DI\Definitions\ServiceDefinition())
			->setType(Commands\InitializeCommand::class);

		// Database repositories
		$builder->addDefinition($this->prefix('models.dashboardRepository'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Dashboards\DashboardRepository::class);

		$builder->addDefinition($this->prefix('models.groupRepository'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Groups\GroupRepository::class);

		$builder->addDefinition($this->prefix('models.widgetRepository'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Widgets\WidgetRepository::class);

		$builder->addDefinition($this->prefix('models.dataSourceRepository'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Widgets\DataSources\DataSourceRepository::class);

		// Database managers
		$builder->addDefinition($this->prefix('models.dashboardsManager'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Dashboards\DashboardsManager::class)
			->setArgument('entityCrud', '__placeholder__');

		$builder->addDefinition($this->prefix('models.groupsManager'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Groups\GroupsManager::class)
			->setArgument('entityCrud', '__placeholder__');

		$builder->addDefinition($this->prefix('models.widgetsManager'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Widgets\WidgetsManager::class)
			->setArgument('entityCrud', '__placeholder__');

		$builder->addDefinition($this->prefix('models.dataSourcesManager'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Widgets\DataSources\DataSourcesManager::class)
			->setArgument('entityCrud', '__placeholder__');

		$builder->addDefinition($this->prefix('models.displaysManager'), new DI\Definitions\ServiceDefinition())
			->setType(Models\Widgets\Displays\DisplaysManager::class)
			->setArgument('entityCrud', '__placeholder__');

		// API controllers
		$builder->addDefinition($this->prefix('controllers.dashboards'), new DI\Definitions\ServiceDefinition())
			->setType(Controllers\DashboardsV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition($this->prefix('controllers.groups'), new DI\Definitions\ServiceDefinition())
			->setType(Controllers\GroupsV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition($this->prefix('controllers.widgets'), new DI\Definitions\ServiceDefinition())
			->setType(Controllers\WidgetsV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition($this->prefix('controllers.dataSources'), new DI\Definitions\ServiceDefinition())
			->setType(Controllers\DataSourcesV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition($this->prefix('controllers.display'), new DI\Definitions\ServiceDefinition())
			->setType(Controllers\DisplayV1Controller::class)
			->addTag('nette.inject');

		// API schemas
		$builder->addDefinition($this->prefix('schemas.dashboard'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Dashboards\DashboardSchema::class);

		$builder->addDefinition($this->prefix('schemas.group'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Groups\GroupSchema::class);

		$builder->addDefinition($this->prefix('schemas.widgets.analogActuator'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\AnalogActuatorSchema::class);

		$builder->addDefinition($this->prefix('schemas.widgets.analogSensor'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\AnalogSensorSchema::class);

		$builder->addDefinition($this->prefix('schemas.widgets.digitalActuator'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\DigitalActuatorSchema::class);

		$builder->addDefinition($this->prefix('schemas.widgets.digitalSensor'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\DigitalSensorSchema::class);

		$builder->addDefinition($this->prefix('schemas.dataSources.channelProperty'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\DataSources\ChannelPropertyDataSourceSchema::class);

		$builder->addDefinition($this->prefix('schemas.display.analogValue'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\Display\AnalogValueSchema::class);

		$builder->addDefinition($this->prefix('schemas.display.button'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\Display\ButtonSchema::class);

		$builder->addDefinition($this->prefix('schemas.display.chartGraph'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\Display\ChartGraphSchema::class);

		$builder->addDefinition($this->prefix('schemas.display.digitalValue'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\Display\DigitalValueSchema::class);

		$builder->addDefinition($this->prefix('schemas.display.gauge'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\Display\GaugeSchema::class);

		$builder->addDefinition($this->prefix('schemas.display.groupedButton'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\Display\GroupedButtonSchema::class);

		$builder->addDefinition($this->prefix('schemas.display.slider'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Widgets\Display\SliderSchema::class);

		// API hydrators
		$builder->addDefinition($this->prefix('hydrators.dashboard'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Dashboards\DashboardHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.group'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Groups\GroupHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.analogActuator'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\AnalogActuatorWidgetHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.analogSensor'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\AnalogSensorWidgetHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.digitalActuator'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\DigitalActuatorWidgetHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.digitalSensor'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\DigitalSensorWidgetHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.dataSources.channelProperty'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\DataSources\ChannelPropertyDataSourceHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.analogValue'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\Displays\AnalogValueHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.button'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\Displays\ButtonHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.chartGraph'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\Displays\ChartGraphHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.digitalValue'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\Displays\DigitalValueHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.gauge'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\Displays\GaugeHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.groupedButton'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\Displays\GroupedButtonHydrator::class);

		$builder->addDefinition($this->prefix('hydrators.widgets.slider'), new DI\Definitions\ServiceDefinition())
			->setType(Hydrators\Widgets\Displays\SliderHydrator::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeCompile(): void
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		/**
		 * Doctrine entities
		 */

		$ormAnnotationDriverService = $builder->getDefinition('nettrineOrmAnnotations.annotationDriver');

		if ($ormAnnotationDriverService instanceof DI\Definitions\ServiceDefinition) {
			$ormAnnotationDriverService->addSetup('addPaths', [[__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Entities']]);
		}

		$ormAnnotationDriverChainService = $builder->getDefinitionByType(Persistence\Mapping\Driver\MappingDriverChain::class);

		if ($ormAnnotationDriverChainService instanceof DI\Definitions\ServiceDefinition) {
			$ormAnnotationDriverChainService->addSetup('addDriver', [
				$ormAnnotationDriverService,
				'FastyBird\UIModule\Entities',
			]);
		}

		/**
		 * Routes
		 */

		$routerService = $builder->getDefinitionByType(SlimRouterRouting\Router::class);

		if ($routerService instanceof DI\Definitions\ServiceDefinition) {
			$routerService->addSetup('?->registerRoutes(?)', [$builder->getDefinitionByType(Router\Routes::class), $routerService]);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function afterCompile(
		PhpGenerator\ClassType $class
	): void {
		$builder = $this->getContainerBuilder();

		$entityFactoryServiceName = $builder->getByType(DoctrineCrud\Crud\IEntityCrudFactory::class, true);

		$dashboardsManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__models__dashboardsManager');
		$dashboardsManagerService->setBody('return new ' . Models\Dashboards\DashboardsManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Dashboards\Dashboard::class . '\'));');

		$groupsManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__models__groupsManager');
		$groupsManagerService->setBody('return new ' . Models\Groups\GroupsManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Groups\Group::class . '\'));');

		$widgetsManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__models__widgetsManager');
		$widgetsManagerService->setBody('return new ' . Models\Widgets\WidgetsManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Widgets\Widget::class . '\'));');

		$dataSourcesManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__models__dataSourcesManager');
		$dataSourcesManagerService->setBody('return new ' . Models\Widgets\DataSources\DataSourcesManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Widgets\DataSources\DataSource::class . '\'));');

		$displaysManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__models__displaysManager');
		$displaysManagerService->setBody('return new ' . Models\Widgets\Displays\DisplaysManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Widgets\Display\Display::class . '\'));');
	}

	/**
	 * @return string[]
	 */
	public function getTranslationResources(): array
	{
		return [
			__DIR__ . '/../Translations',
		];
	}

}
