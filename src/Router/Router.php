<?php declare(strict_types = 1);

/**
 * RouterFactory.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Router
 * @since          0.1.0
 *
 * @date           26.05.20
 */

namespace FastyBird\UIModule\Router;

use FastyBird\SimpleAuth\Middleware as SimpleAuthMiddleware;
use FastyBird\UIModule\Controllers;
use FastyBird\UIModule\Middleware;
use IPub\SlimRouter\Routing;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Module router configuration
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Router
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Router extends Routing\Router
{

	public const URL_ITEM_ID = 'id';

	public const URL_DASHBOARD_ID = 'dashboard';
	public const URL_WIDGET_ID = 'widget';

	public const RELATION_ENTITY = 'relationEntity';

	/** @var Controllers\DashboardsV1Controller */
	private $dashboardsV1Controller;

	/** @var Controllers\GroupsV1Controller */
	private $groupsV1Controller;

	/** @var Controllers\WidgetsV1Controller */
	private $widgetsV1Controller;

	/** @var Controllers\DisplayV1Controller */
	private $displayV1Controller;

	/** @var Controllers\DataSourcesV1Controller */
	private $dataSourceV1Controller;

	/** @var Middleware\AccessMiddleware */
	private $uiAccessControlMiddleware;

	/** @var SimpleAuthMiddleware\AccessMiddleware */
	private $accessControlMiddleware;

	/** @var SimpleAuthMiddleware\UserMiddleware */
	private $userMiddleware;

	public function __construct(
		Controllers\DashboardsV1Controller $dashboardsV1Controller,
		Controllers\GroupsV1Controller $groupsV1Controller,
		Controllers\WidgetsV1Controller $widgetsV1Controller,
		Controllers\DisplayV1Controller $displayV1Controller,
		Controllers\DataSourcesV1Controller $dataSourceV1Controller,
		Middleware\AccessMiddleware $uiAccessControlMiddleware,
		SimpleAuthMiddleware\AccessMiddleware $accessControlMiddleware,
		SimpleAuthMiddleware\UserMiddleware $userMiddleware,
		?ResponseFactoryInterface $responseFactory = null
	) {
		parent::__construct($responseFactory, null);

		$this->dashboardsV1Controller = $dashboardsV1Controller;
		$this->groupsV1Controller = $groupsV1Controller;
		$this->widgetsV1Controller = $widgetsV1Controller;
		$this->displayV1Controller = $displayV1Controller;
		$this->dataSourceV1Controller = $dataSourceV1Controller;

		$this->uiAccessControlMiddleware = $uiAccessControlMiddleware;
		$this->accessControlMiddleware = $accessControlMiddleware;
		$this->userMiddleware = $userMiddleware;
	}

	/**
	 * @return void
	 */
	public function registerRoutes(): void
	{
		$routes = $this->group('/v1', function (Routing\RouteCollector $group): void {
			$group->group('/dashboards', function (Routing\RouteCollector $group): void {
				/**
				 * DASHBOARDS
				 */
				$route = $group->get('', [$this->dashboardsV1Controller, 'index']);
				$route->setName('dashboards');

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->dashboardsV1Controller, 'read']);
				$route->setName('dashboard');

				$group->post('', [$this->dashboardsV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->dashboardsV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->dashboardsV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->dashboardsV1Controller, 'readRelationship']);
				$route->setName('dashboard.relationship');
			});

			$group->group('/dashboards/{' . self::URL_DASHBOARD_ID . '}', function (Routing\RouteCollector $group): void {
				$group->group('/groups', function (Routing\RouteCollector $group): void {
					/**
					 * DASHBOARD GROUPS
					 */
					$route = $group->get('', [$this->groupsV1Controller, 'index']);
					$route->setName('dashboard.groups');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->groupsV1Controller, 'read']);
					$route->setName('dashboard.group');

					$group->post('', [$this->groupsV1Controller, 'create']);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->groupsV1Controller, 'update']);

					$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->groupsV1Controller, 'delete']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->groupsV1Controller, 'readRelationship']);
					$route->setName('dashboard.group.relationship');
				});
			});

			$group->group('/widgets', function (Routing\RouteCollector $group): void {
				/**
				 * WIDGETS
				 */
				$route = $group->get('', [$this->widgetsV1Controller, 'index']);
				$route->setName('widgets');

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->widgetsV1Controller, 'read']);
				$route->setName('widget');

				$group->post('', [$this->widgetsV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->widgetsV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->widgetsV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->widgetsV1Controller, 'readRelationship']);
				$route->setName('widget.relationship');
			});

			$group->group('/widgets/{' . self::URL_WIDGET_ID . '}', function (Routing\RouteCollector $group): void {
				$group->group('/display', function (Routing\RouteCollector $group): void {
					/**
					 * WIDGET DISPLAY
					 */
					$route = $group->get('', [$this->displayV1Controller, 'read']);
					$route->setName('widget.display');

					$group->patch('', [$this->displayV1Controller, 'update']);

					$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->displayV1Controller, 'readRelationship']);
					$route->setName('widget.display.relationship');
				});

				$group->group('/data-sources', function (Routing\RouteCollector $group): void {
					/**
					 * WIDGET DATA SOURCES
					 */
					$route = $group->get('', [$this->dataSourceV1Controller, 'index']);
					$route->setName('widget.data-sources');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->dataSourceV1Controller, 'read']);
					$route->setName('widget.data-source');

					$group->post('', [$this->dataSourceV1Controller, 'create']);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->dataSourceV1Controller, 'update']);

					$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->dataSourceV1Controller, 'delete']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->dataSourceV1Controller, 'readRelationship']);
					$route->setName('widget.data-source.relationship');
				});
			});
		});

		$routes->addMiddleware($this->accessControlMiddleware);
		$routes->addMiddleware($this->userMiddleware);
		$routes->addMiddleware($this->uiAccessControlMiddleware);
	}

}
