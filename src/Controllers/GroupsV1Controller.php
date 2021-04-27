<?php declare(strict_types = 1);

/**
 * GroupsV1Controller.php
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
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Hydrators;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Queries;
use FastyBird\UIModule\Router;
use FastyBird\UIModule\Schemas;
use FastyBird\WebServer\Http as WebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use IPub\DoctrineCrud\Exceptions as DoctrineCrudExceptions;
use Psr\Http\Message;
use Ramsey\Uuid;
use Throwable;

/**
 * API groups controller
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class GroupsV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TDashboardFinder;

	/** @var Models\Dashboards\IDashboardRepository */
	protected Models\Dashboards\IDashboardRepository $dashboardRepository;

	/** @var string */
	protected string $translationDomain = 'ui-module.groups';

	/** @var Hydrators\Groups\GroupHydrator */
	private Hydrators\Groups\GroupHydrator $groupsHydrator;

	/** @var Models\Groups\IGroupRepository */
	private Models\Groups\IGroupRepository $groupRepository;

	/** @var Models\Groups\IGroupsManager */
	private Models\Groups\IGroupsManager $groupsManager;

	/**
	 * @param Models\Groups\IGroupRepository $groupRepository
	 * @param Models\Groups\IGroupsManager $groupsManager
	 * @param Models\Dashboards\IDashboardRepository $dashboardRepository
	 * @param Hydrators\Groups\GroupHydrator $groupsHydrator
	 */
	public function __construct(
		Models\Groups\IGroupRepository $groupRepository,
		Models\Groups\IGroupsManager $groupsManager,
		Models\Dashboards\IDashboardRepository $dashboardRepository,
		Hydrators\Groups\GroupHydrator $groupsHydrator
	) {
		$this->groupRepository = $groupRepository;
		$this->groupsManager = $groupsManager;
		$this->dashboardRepository = $dashboardRepository;
		$this->groupsHydrator = $groupsHydrator;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param WebServerHttp\Response $response
	 *
	 * @return WebServerHttp\Response
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	public function index(
		Message\ServerRequestInterface $request,
		WebServerHttp\Response $response
	): WebServerHttp\Response {
		// At first, try to load dashboard
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$findQuery = new Queries\FindGroupsQuery();
		$findQuery->forDashboard($dashboard);

		$groups = $this->groupRepository->getResultSet($findQuery);

		return $response
			->withEntity(WebServerHttp\ScalarEntity::from($groups));
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
		// At first, try to load dashboard
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$group = $this->findGroup($request->getAttribute(Router\Routes::URL_ITEM_ID), $dashboard);

		return $response
			->withEntity(WebServerHttp\ScalarEntity::from($group));
	}

	/**
	 * @param string $id
	 * @param Entities\Dashboards\IDashboard $dashboard
	 *
	 * @return Entities\Groups\IGroup
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	private function findGroup(
		string $id,
		Entities\Dashboards\IDashboard $dashboard
	): Entities\Groups\IGroup {
		try {
			$findQuery = new Queries\FindGroupsQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));
			$findQuery->forDashboard($dashboard);

			$group = $this->groupRepository->findOneBy($findQuery);

			if ($group === null) {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('messages.notFound.heading'),
					$this->translator->translate('messages.notFound.message')
				);
			}

		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		return $group;
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
		// At first, try to load dashboard
		$this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$document = $this->createDocument($request);

		if ($document->getResource()->getType() === Schemas\Groups\GroupSchema::SCHEMA_TYPE) {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$group = $this->groupsManager->create($this->groupsHydrator->hydrate($document));

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

				// Log caught exception
				$this->logger->error('[FB:UI_MODULE:CONTROLLER] ' . $ex->getMessage(), [
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
				->withEntity(WebServerHttp\ScalarEntity::from($group))
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

		// At first, try to load dashboard
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$group = $this->findGroup($request->getAttribute(Router\Routes::URL_ITEM_ID), $dashboard);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\Groups\GroupSchema::SCHEMA_TYPE) {
				$updateGroupData = $this->groupsHydrator->hydrate($document, $group);

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

			$group = $this->groupsManager->update($group, $updateGroupData);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (JsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			// Log caught exception
			$this->logger->error('[FB:UI_MODULE:CONTROLLER] ' . $ex->getMessage(), [
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
			->withEntity(WebServerHttp\ScalarEntity::from($group));
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
		// At first, try to load dashboard
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$group = $this->findGroup($request->getAttribute(Router\Routes::URL_ITEM_ID), $dashboard);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Move device back into warehouse
			$this->groupsManager->delete($group);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Log caught exception
			$this->logger->error('[FB:UI_MODULE:CONTROLLER] ' . $ex->getMessage(), [
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
		// At first, try to load dashboard
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$group = $this->findGroup($request->getAttribute(Router\Routes::URL_ITEM_ID), $dashboard);

		$relationEntity = strtolower($request->getAttribute(Router\Routes::RELATION_ENTITY));

		if ($relationEntity === Schemas\Groups\GroupSchema::RELATIONSHIPS_DASHBOARD) {
			return $response
				->withEntity(WebServerHttp\ScalarEntity::from($group->getDashboard()));

		} elseif ($relationEntity === Schemas\Groups\GroupSchema::RELATIONSHIPS_WIDGETS) {
			return $response
				->withEntity(WebServerHttp\ScalarEntity::from($group->getWidgets()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

}
