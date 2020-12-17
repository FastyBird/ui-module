<?php declare(strict_types = 1);

/**
 * DevicesController.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           26.05.20
 */

namespace FastyBird\UIModule\Controllers;

use Doctrine;
use FastyBird\JsonApi\Exceptions as JsonApiExceptions;
use FastyBird\UIModule\Controllers;
use FastyBird\UIModule\Hydrators;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Queries;
use FastyBird\UIModule\Router;
use FastyBird\UIModule\Schemas;
use FastyBird\WebServer\Http as WebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use IPub\DoctrineCrud\Exceptions as DoctrineCrudExceptions;
use Psr\Http\Message;
use Throwable;

/**
 * API dashboards controller
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DashboardsV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TDashboardFinder;

	/** @var string */
	protected string $translationDomain = 'ui-module.dashboards';

	/** @var Hydrators\Dashboards\DashboardHydrator */
	private Hydrators\Dashboards\DashboardHydrator $dashboardHydrator;

	/** @var Models\Dashboards\IDashboardRepository */
	private Models\Dashboards\IDashboardRepository $dashboardRepository;

	/** @var Models\Dashboards\IDashboardsManager */
	private Models\Dashboards\IDashboardsManager $dashboardsManager;

	/** @var Models\Groups\IGroupsManager */
	private Models\Groups\IGroupsManager $groupsManager;

	/**
	 * @param Models\Dashboards\IDashboardRepository $dashboardRepository
	 * @param Models\Dashboards\IDashboardsManager $dashboardsManager
	 * @param Models\Groups\IGroupsManager $groupsManager
	 * @param Hydrators\Dashboards\DashboardHydrator $dashboardHydrator
	 */
	public function __construct(
		Models\Dashboards\IDashboardRepository $dashboardRepository,
		Models\Dashboards\IDashboardsManager $dashboardsManager,
		Models\Groups\IGroupsManager $groupsManager,
		Hydrators\Dashboards\DashboardHydrator $dashboardHydrator
	) {
		$this->dashboardRepository = $dashboardRepository;
		$this->dashboardsManager = $dashboardsManager;
		$this->groupsManager = $groupsManager;
		$this->dashboardHydrator = $dashboardHydrator;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param WebServerHttp\Response $response
	 *
	 * @return WebServerHttp\Response
	 */
	public function index(
		Message\ServerRequestInterface $request,
		WebServerHttp\Response $response
	): WebServerHttp\Response {
		$findQuery = new Queries\FindDashboardsQuery();

		$dashboards = $this->dashboardRepository->getResultSet($findQuery);

		return $response
			->withEntity(WebServerHttp\ScalarEntity::from($dashboards));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param WebServerHttp\Response $response
	 *
	 * @return WebServerHttp\Response
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		WebServerHttp\Response $response
	): WebServerHttp\Response {
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_ITEM_ID));

		return $response
			->withEntity(WebServerHttp\ScalarEntity::from($dashboard));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param WebServerHttp\Response $response
	 *
	 * @return WebServerHttp\Response
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function create(
		Message\ServerRequestInterface $request,
		WebServerHttp\Response $response
	): WebServerHttp\Response {
		$document = $this->createDocument($request);

		if ($document->getResource()->getType() === Schemas\Dashboards\DashboardSchema::SCHEMA_TYPE) {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$dashboard = $this->dashboardsManager->create($this->dashboardHydrator->hydrate($document));

				// Commit all changes into database
				$this->getOrmConnection()->commit();

			} catch (JsonApiExceptions\IJsonApiException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				throw $ex;

			} catch (DoctrineCrudExceptions\MissingRequiredFieldException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				$pointer = 'data/attributes/' . $ex->getField();

				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.missingRequired.heading'),
					$this->translator->translate('//ui-module.base.messages.missingRequired.message'),
					[
						'pointer' => $pointer,
					]
				);

			} catch (DoctrineCrudExceptions\EntityCreationException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.missingRequired.heading'),
					$this->translator->translate('//ui-module.base.messages.missingRequired.message'),
					[
						'pointer' => 'data/attributes/' . $ex->getField(),
					]
				);

			} catch (Throwable $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				// Log catched exception
				$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
					'exception' => [
						'message' => $ex->getMessage(),
						'code'    => $ex->getCode(),
					],
				]);

				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.notCreated.heading'),
					$this->translator->translate('messages.notCreated.message')
				);
			}

			/** @var WebServerHttp\Response $response */
			$response = $response
				->withEntity(WebServerHttp\ScalarEntity::from($dashboard))
				->withStatus(StatusCodeInterface::STATUS_CREATED);

			return $response;
		}

		throw new JsonApiExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
			$this->translator->translate('messages.invalidType.heading'),
			$this->translator->translate('messages.invalidType.message'),
			[
				'pointer' => '/data/type',
			]
		);
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param WebServerHttp\Response $response
	 *
	 * @return WebServerHttp\Response
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
		Message\ServerRequestInterface $request,
		WebServerHttp\Response $response
	): WebServerHttp\Response {
		$document = $this->createDocument($request);

		if ($request->getAttribute(Router\Routes::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//ui-module.base.messages.invalid.heading'),
				$this->translator->translate('//ui-module.base.messages.invalid.message')
			);
		}

		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\Dashboards\DashboardSchema::SCHEMA_TYPE) {
				$updateDashboardData = $this->dashboardHydrator->hydrate($document, $dashboard);

			} else {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.invalidType.heading'),
					$this->translator->translate('messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			$dashboard = $this->dashboardsManager->update($dashboard, $updateDashboardData);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (JsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notUpdated.heading'),
				$this->translator->translate('messages.notUpdated.message')
			);
		}

		return $response
			->withEntity(WebServerHttp\ScalarEntity::from($dashboard));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param WebServerHttp\Response $response
	 *
	 * @return WebServerHttp\Response
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function delete(
		Message\ServerRequestInterface $request,
		WebServerHttp\Response $response
	): WebServerHttp\Response {
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			foreach ($dashboard->getGroups() as $group) {
				// Remove channels. Newly connected device will be reinitialized with all channels
				$this->groupsManager->delete($group);
			}

			// Move device back into warehouse
			$this->dashboardsManager->delete($dashboard);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notDeleted.heading'),
				$this->translator->translate('messages.notDeleted.message')
			);
		}

		/** @var WebServerHttp\Response $response */
		$response = $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param WebServerHttp\Response $response
	 *
	 * @return WebServerHttp\Response
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		WebServerHttp\Response $response
	): WebServerHttp\Response {
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_ITEM_ID));

		$relationEntity = strtolower($request->getAttribute(Router\Routes::RELATION_ENTITY));

		if ($relationEntity === Schemas\Dashboards\DashboardSchema::RELATIONSHIPS_GROUPS) {
			return $response
				->withEntity(WebServerHttp\ScalarEntity::from($dashboard->getGroups()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

}
