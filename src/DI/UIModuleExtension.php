<?php declare(strict_types = 1);

/**
 * UIModuleExtension.php
 *
 * @license        More in license.md
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
use FastyBird\UIModule\Events;
use FastyBird\UIModule\Hydrators;
use FastyBird\UIModule\Middleware;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Router;
use FastyBird\UIModule\Schemas;
use FastyBird\UIModule\Sockets;
use FastyBird\WebServer\Commands as WebServerCommands;
use IPub\DoctrineCrud;
use IPub\WebSockets;
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
			'keys' => Schema\Expect::string()->default(null),
			'origins' => Schema\Expect::string()->default(null),
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
		$builder->addDefinition(null)
			->setType(Middleware\AccessMiddleware::class);

		$builder->addDefinition(null)
			->setType(Router\Routes::class);

		// Console commands
		$builder->addDefinition(null)
			->setType(Commands\InitializeCommand::class);

		// Events
		$builder->addDefinition($this->prefix('event.wsClientConnect'))
			->setType(Events\WsClientConnectedHandler::class)
			->setArgument('wsKeys', $configuration->keys)
			->setArgument('allowedOrigins', $configuration->origins);

		$builder->addDefinition($this->prefix('event.wsMessage'))
			->setType(Events\WsMessageHandler::class)
			->setArgument('wsKeys', $configuration->keys)
			->setArgument('allowedOrigins', $configuration->origins);

		$builder->addDefinition($this->prefix('event.socketConnect'))
			->setType(Events\ServerSocketConnectHandler::class);

		// Database repositories
		$builder->addDefinition(null)
			->setType(Models\Dashboards\DashboardRepository::class);

		$builder->addDefinition(null)
			->setType(Models\Groups\GroupRepository::class);

		$builder->addDefinition(null)
			->setType(Models\Widgets\WidgetRepository::class);

		$builder->addDefinition(null)
			->setType(Models\Widgets\DataSources\DataSourceRepository::class);

		// Database managers
		$builder->addDefinition($this->prefix('doctrine.dashboardsManager'))
			->setType(Models\Dashboards\DashboardsManager::class)
			->setArgument('entityCrud', '__placeholder__');

		$builder->addDefinition($this->prefix('doctrine.groupsManager'))
			->setType(Models\Groups\GroupsManager::class)
			->setArgument('entityCrud', '__placeholder__');

		$builder->addDefinition($this->prefix('doctrine.widgetsManager'))
			->setType(Models\Widgets\WidgetsManager::class)
			->setArgument('entityCrud', '__placeholder__');

		$builder->addDefinition($this->prefix('doctrine.dataSourcesManager'))
			->setType(Models\Widgets\DataSources\DataSourcesManager::class)
			->setArgument('entityCrud', '__placeholder__');

		$builder->addDefinition($this->prefix('doctrine.displaysManager'))
			->setType(Models\Widgets\Displays\DisplaysManager::class)
			->setArgument('entityCrud', '__placeholder__');

		// API controllers
		$builder->addDefinition(null)
			->setType(Controllers\DashboardsV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition(null)
			->setType(Controllers\GroupsV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition(null)
			->setType(Controllers\WidgetsV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition(null)
			->setType(Controllers\DataSourcesV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition(null)
			->setType(Controllers\DisplayV1Controller::class)
			->addTag('nette.inject');

		$builder->addDefinition(null)
			->setType(Controllers\ExchangeController::class)
			->addTag('nette.inject');

		// API schemas
		$builder->addDefinition(null)
			->setType(Schemas\Dashboards\DashboardSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Groups\GroupSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\AnalogActuatorSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\AnalogSensorSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\DigitalActuatorSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\DigitalSensorSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\DataSources\ChannelPropertyDataSourceSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\Display\AnalogValueSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\Display\ButtonSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\Display\ChartGraphSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\Display\DigitalValueSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\Display\GaugeSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\Display\GroupedButtonSchema::class);

		$builder->addDefinition(null)
			->setType(Schemas\Widgets\Display\SliderSchema::class);

		// API hydrators
		$builder->addDefinition(null)
			->setType(Hydrators\Dashboards\DashboardHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Groups\GroupHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\AnalogActuatorWidgetHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\AnalogSensorWidgetHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\DigitalActuatorWidgetHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\DigitalSensorWidgetHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\DataSources\ChannelPropertyDataSourceHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\Displays\AnalogValueHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\Displays\ButtonHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\Displays\ChartGraphHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\Displays\DigitalValueHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\Displays\GaugeHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\Displays\GroupedButtonHydrator::class);

		$builder->addDefinition(null)
			->setType(Hydrators\Widgets\Displays\SliderHydrator::class);

		// Sockets
		$builder->addDefinition(null)
			->setType(Sockets\Sender::class);
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
		 * SERVER EVENTS
		 */

		$serverCommandServiceName = $builder->getByType(WebServerCommands\HttpServerCommand::class);

		if ($serverCommandServiceName !== null) {
			/** @var DI\Definitions\ServiceDefinition $serverCommandService */
			$serverCommandService = $builder->getDefinition($serverCommandServiceName);

			$serverCommandService
				->addSetup('$onSocketConnect[]', ['@' . $this->prefix('event.socketConnect')]);
		}

		$socketWrapperServiceName = $builder->getByType(WebSockets\Server\Wrapper::class);

		if ($socketWrapperServiceName !== null) {
			/** @var DI\Definitions\ServiceDefinition $socketWrapperService */
			$socketWrapperService = $builder->getDefinition($socketWrapperServiceName);

			$socketWrapperService
				->addSetup('$onClientConnected[]', ['@' . $this->prefix('event.wsClientConnect')])
				->addSetup('$onIncomingMessage[]', ['@' . $this->prefix('event.wsMessage')]);
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

		$dashboardsManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__doctrine__dashboardsManager');
		$dashboardsManagerService->setBody('return new ' . Models\Dashboards\DashboardsManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Dashboards\Dashboard::class . '\'));');

		$groupsManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__doctrine__groupsManager');
		$groupsManagerService->setBody('return new ' . Models\Groups\GroupsManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Groups\Group::class . '\'));');

		$widgetsManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__doctrine__widgetsManager');
		$widgetsManagerService->setBody('return new ' . Models\Widgets\WidgetsManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Widgets\Widget::class . '\'));');

		$dataSourcesManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__doctrine__dataSourcesManager');
		$dataSourcesManagerService->setBody('return new ' . Models\Widgets\DataSources\DataSourcesManager::class . '($this->getService(\'' . $entityFactoryServiceName . '\')->create(\'' . Entities\Widgets\DataSources\DataSource::class . '\'));');

		$displaysManagerService = $class->getMethod('createService' . ucfirst($this->name) . '__doctrine__displaysManager');
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
