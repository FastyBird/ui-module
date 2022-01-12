<?php declare(strict_types = 1);

/**
 * GroupsV1Controller.php
 *
 * @license        More in LICENSE.md
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
	 * @param Message\ResponseInterface $response
	 *
	 * @return Message\ResponseInterface
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	public function index(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response
	): Message\ResponseInterface {
		// At first, try to load dashboard
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$findQuery = new Queries\FindGroupsQuery();
		$findQuery->forDashboard($dashboard);

		$groups = $this->groupRepository->getResultSet($findQuery);

		// @phpstan-ignore-next-line
		return $this->buildResponse($request, $response, $groups);
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param Message\ResponseInterface $response
	 *
	 * @return Message\ResponseInterface
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response
	): Message\ResponseInterface {
		// At first, try to load dashboard
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$group = $this->findGroup($request->getAttribute(Router\Routes::URL_ITEM_ID), $dashboard);

		return $this->buildResponse($request, $response, $group);
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
					$this->translator->translate('//ui-module.groups.messages.notFound.heading'),
					$this->translator->translate('//ui-module.groups.messages.notFound.message')
				);
			}
		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//ui-module.groups.messages.notFound.heading'),
				$this->translator->translate('//ui-module.groups.messages.notFound.message')
			);
		}

		return $group;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param Message\ResponseInterface $response
	 *
	 * @return Message\ResponseInterface
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function create(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response
	): Message\ResponseInterface {
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
				$this->logger->error('An unhandled error occurred', [
					'source'    => 'ui-module-groups-controller',
					'type'      => 'create',
					'exception' => [
						'message' => $ex->getMessage(),
						'code'    => $ex->getCode(),
					],
				]);

				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.groups.messages.notCreated.heading'),
					$this->translator->translate('//ui-module.groups.messages.notCreated.message')
				);
			}

			$response = $this->buildResponse($request, $response, $group);
			return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
		}

		throw new JsonApiExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
			$this->translator->translate('//ui-module.groups.messages.invalidType.heading'),
			$this->translator->translate('//ui-module.groups.messages.invalidType.message'),
			[
				'pointer' => '/data/type',
			]
		);
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param Message\ResponseInterface $response
	 *
	 * @return Message\ResponseInterface
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response
	): Message\ResponseInterface {
		$document = $this->createDocument($request);

		$this->validateIdentifier($request, $document);

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
					$this->translator->translate('//ui-module.groups.messages.invalidType.heading'),
					$this->translator->translate('//ui-module.groups.messages.invalidType.message'),
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
			$this->logger->error('An unhandled error occurred', [
				'source'    => 'ui-module-groups-controller',
				'type'      => 'update',
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.groups.messages.notUpdated.heading'),
				$this->translator->translate('//ui-module.groups.messages.notUpdated.message')
			);
		}

		return $this->buildResponse($request, $response, $group);
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param Message\ResponseInterface $response
	 *
	 * @return Message\ResponseInterface
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function delete(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response
	): Message\ResponseInterface {
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
			$this->logger->error('An unhandled error occurred', [
				'source'    => 'ui-module-groups-controller',
				'type'      => 'delete',
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.groups.messages.notDeleted.heading'),
				$this->translator->translate('//ui-module.groups.messages.notDeleted.message')
			);
		}

		return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param Message\ResponseInterface $response
	 *
	 * @return Message\ResponseInterface
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response
	): Message\ResponseInterface {
		// At first, try to load dashboard
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_DASHBOARD_ID));

		$group = $this->findGroup($request->getAttribute(Router\Routes::URL_ITEM_ID), $dashboard);

		$relationEntity = strtolower($request->getAttribute(Router\Routes::RELATION_ENTITY));

		if ($relationEntity === Schemas\Groups\GroupSchema::RELATIONSHIPS_DASHBOARD) {
			return $this->buildResponse($request, $response, $group->getDashboard());

		} elseif ($relationEntity === Schemas\Groups\GroupSchema::RELATIONSHIPS_WIDGETS) {
			return $this->buildResponse($request, $response, $group->getWidgets());
		}

		return parent::readRelationship($request, $response);
	}

}
