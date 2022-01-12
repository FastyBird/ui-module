<?php declare(strict_types = 1);

/**
 * DevicesController.php
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
use FastyBird\UIModule\Hydrators;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Queries;
use FastyBird\UIModule\Router;
use FastyBird\UIModule\Schemas;
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
	 * @param Message\ResponseInterface $response
	 *
	 * @return Message\ResponseInterface
	 */
	public function index(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response
	): Message\ResponseInterface {
		$findQuery = new Queries\FindDashboardsQuery();

		$dashboards = $this->dashboardRepository->getResultSet($findQuery);

		// @phpstan-ignore-next-line
		return $this->buildResponse($request, $response, $dashboards);
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
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_ITEM_ID));

		return $this->buildResponse($request, $response, $dashboard);
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

				// Log caught exception
				$this->logger->error('An unhandled error occurred', [
					'source'    => 'ui-module-dashboards-controller',
					'type'      => 'create',
					'exception' => [
						'message' => $ex->getMessage(),
						'code'    => $ex->getCode(),
					],
				]);

				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.dashboards.messages.notCreated.heading'),
					$this->translator->translate('//ui-module.dashboards.messages.notCreated.message')
				);
			}

			$response = $this->buildResponse($request, $response, $dashboard);
			return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
		}

		throw new JsonApiExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
			$this->translator->translate('//ui-module.dashboards.messages.invalidType.heading'),
			$this->translator->translate('//ui-module.dashboards.messages.invalidType.message'),
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

		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\Dashboards\DashboardSchema::SCHEMA_TYPE) {
				$updateDashboardData = $this->dashboardHydrator->hydrate($document, $dashboard);

			} else {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.dashboards.messages.invalidType.heading'),
					$this->translator->translate('//ui-module.dashboards.messages.invalidType.message'),
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

			// Log caught exception
			$this->logger->error('An unhandled error occurred', [
				'source'    => 'ui-module-dashboards-controller',
				'type'      => 'update',
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.dashboards.messages.notUpdated.heading'),
				$this->translator->translate('//ui-module.dashboards.messages.notUpdated.message')
			);
		}

		return $this->buildResponse($request, $response, $dashboard);
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
			// Log caught exception
			$this->logger->error('An unhandled error occurred', [
				'source'    => 'ui-module-dashboards-controller',
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
				$this->translator->translate('//ui-module.dashboards.messages.notDeleted.heading'),
				$this->translator->translate('//ui-module.dashboards.messages.notDeleted.message')
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
		$dashboard = $this->findDashboard($request->getAttribute(Router\Routes::URL_ITEM_ID));

		$relationEntity = strtolower($request->getAttribute(Router\Routes::RELATION_ENTITY));

		if ($relationEntity === Schemas\Dashboards\DashboardSchema::RELATIONSHIPS_GROUPS) {
			return $this->buildResponse($request, $response, $dashboard->getGroups());
		}

		return parent::readRelationship($request, $response);
	}

}
