<?php declare(strict_types = 1);

/**
 * WidgetsV1Controller.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 * @since          1.0.0
 *
 * @date           26.05.20
 */

namespace FastyBird\Module\Ui\Controllers;

use Doctrine;
use Exception;
use FastyBird\JsonApi\Exceptions as JsonApiExceptions;
use FastyBird\Library\Application\Exceptions as ApplicationExceptions;
use FastyBird\Library\Application\Helpers as ApplicationHelpers;
use FastyBird\Library\Metadata\Types as MetadataTypes;
use FastyBird\Module\Ui\Controllers;
use FastyBird\Module\Ui\Exceptions;
use FastyBird\Module\Ui\Models;
use FastyBird\Module\Ui\Queries;
use FastyBird\Module\Ui\Router;
use FastyBird\Module\Ui\Schemas;
use FastyBird\Module\Ui\Utilities;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use IPub\DoctrineCrud\Exceptions as DoctrineCrudExceptions;
use IPub\DoctrineOrmQuery\Exceptions as DoctrineOrmQueryExceptions;
use Nette\Utils;
use Psr\Http\Message;
use Throwable;
use function end;
use function explode;
use function preg_match;
use function str_starts_with;
use function strval;

/**
 * API widgets controller
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class WidgetsV1 extends BaseV1
{

	use Controllers\Finders\TWidget;

	public function __construct(
		private readonly Models\Entities\Widgets\Repository $widgetsRepository,
		private readonly Models\Entities\Widgets\Manager $widgetsManager,
		private readonly Models\Entities\Dashboards\Repository $dashboardsRepository,
		private readonly Models\Entities\Groups\Repository $groupsRepository,
		private readonly Models\Entities\Widgets\DataSources\Repository $dataSourcesRepository,
		private readonly Models\Entities\Widgets\DataSources\Manager $dataSourcesManager,
	)
	{
	}

	/**
	 * @throws Exception
	 */
	public function index(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response,
	): Message\ResponseInterface
	{
		$findQuery = new Queries\Entities\FindWidgets();

		$widgets = $this->widgetsRepository->getResultSet($findQuery);

		// @phpstan-ignore-next-line
		return $this->buildResponse($request, $response, $widgets);
	}

	/**
	 * @throws Exception
	 * @throws JsonApiExceptions\JsonApi
	 */
	public function read(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response,
	): Message\ResponseInterface
	{
		$widget = $this->findWidget(strval($request->getAttribute(Router\ApiRoutes::URL_ITEM_ID)));

		return $this->buildResponse($request, $response, $widget);
	}

	/**
	 * @throws Doctrine\DBAL\Exception
	 * @throws Exception
	 * @throws JsonApiExceptions\JsonApi
	 * @throws JsonApiExceptions\JsonApiError
	 */
	public function create(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response,
	): Message\ResponseInterface
	{
		$document = $this->createDocument($request);

		$hydrator = $this->hydratorsContainer->findHydrator($document);

		if ($hydrator !== null) {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$widget = $this->widgetsManager->create($hydrator->hydrate($document));

				// Commit all changes into database
				$this->getOrmConnection()->commit();

			} catch (JsonApiExceptions\JsonApi $ex) {
				throw $ex;
			} catch (DoctrineCrudExceptions\MissingRequiredFieldException $ex) {
				throw new JsonApiExceptions\JsonApiError(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.missingAttribute.heading'),
					$this->translator->translate('//ui-module.base.messages.missingAttribute.message'),
					[
						'pointer' => '/data/attributes/' . Utilities\Api::fieldToJsonApi($ex->getField()),
					],
				);
			} catch (DoctrineCrudExceptions\EntityCreationException $ex) {
				throw new JsonApiExceptions\JsonApiError(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.missingAttribute.heading'),
					$this->translator->translate('//ui-module.base.messages.missingAttribute.message'),
					[
						'pointer' => '/data/attributes/' . Utilities\Api::fieldToJsonApi($ex->getField()),
					],
				);
			} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
				if (preg_match("%PRIMARY'%", $ex->getMessage(), $match) === 1) {
					throw new JsonApiExceptions\JsonApiError(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//ui-module.base.messages.uniqueIdentifier.heading'),
						$this->translator->translate('//ui-module.base.messages.uniqueIdentifier.message'),
						[
							'pointer' => '/data/id',
						],
					);
				} elseif (preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match) === 1) {
					$columnParts = explode('.', $match['key']);
					$columnKey = end($columnParts);

					if (str_starts_with($columnKey, 'widget_')) {
						throw new JsonApiExceptions\JsonApiError(
							StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
							$this->translator->translate('//ui-module.base.messages.uniqueAttribute.heading'),
							$this->translator->translate('//ui-module.base.messages.uniqueAttribute.message'),
							[
								'pointer' => '/data/attributes/' . Utilities\Api::fieldToJsonApi(
									Utils\Strings::substring($columnKey, 7),
								),
							],
						);
					}
				}

				throw new JsonApiExceptions\JsonApiError(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.uniqueAttribute.heading'),
					$this->translator->translate('//ui-module.base.messages.uniqueAttribute.message'),
				);
			} catch (Throwable $ex) {
				// Log caught exception
				$this->logger->error(
					'An unhandled error occurred',
					[
						'source' => MetadataTypes\Sources\Module::UI->value,
						'type' => 'widgets-controller',
						'exception' => ApplicationHelpers\Logger::buildException($ex),
					],
				);

				throw new JsonApiExceptions\JsonApiError(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.notCreated.heading'),
					$this->translator->translate('//ui-module.base.messages.notCreated.message'),
				);
			} finally {
				// Revert all changes when error occur
				if ($this->getOrmConnection()->isTransactionActive()) {
					$this->getOrmConnection()->rollBack();
				}
			}

			$response = $this->buildResponse($request, $response, $widget);

			return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
		}

		throw new JsonApiExceptions\JsonApiError(
			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
			$this->translator->translate('//ui-module.base.messages.invalidType.heading'),
			$this->translator->translate('//ui-module.base.messages.invalidType.message'),
			[
				'pointer' => '/data/type',
			],
		);
	}

	/**
	 * @throws Doctrine\DBAL\Exception
	 * @throws Exception
	 * @throws JsonApiExceptions\JsonApi
	 * @throws JsonApiExceptions\JsonApiError
	 */
	public function update(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response,
	): Message\ResponseInterface
	{
		$widget = $this->findWidget(strval($request->getAttribute(Router\ApiRoutes::URL_ITEM_ID)));

		$document = $this->createDocument($request);

		$this->validateIdentifier($request, $document);

		$hydrator = $this->hydratorsContainer->findHydrator($document);

		if ($hydrator !== null) {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$widget = $this->widgetsManager->update($widget, $hydrator->hydrate($document, $widget));

				// Commit all changes into database
				$this->getOrmConnection()->commit();

			} catch (JsonApiExceptions\JsonApi $ex) {
				throw $ex;
			} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
				if (preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match) !== false) {
					$columnParts = explode('.', $match['key']);
					$columnKey = end($columnParts);

					if (str_starts_with($columnKey, 'widget_')) {
						throw new JsonApiExceptions\JsonApiError(
							StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
							$this->translator->translate('//ui-module.base.messages.uniqueAttribute.heading'),
							$this->translator->translate('//ui-module.base.messages.uniqueAttribute.message'),
							[
								'pointer' => '/data/attributes/' . Utilities\Api::fieldToJsonApi(
									Utils\Strings::substring($columnKey, 7),
								),
							],
						);
					}
				}

				throw new JsonApiExceptions\JsonApiError(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.uniqueAttribute.heading'),
					$this->translator->translate('//ui-module.base.messages.uniqueAttribute.message'),
				);
			} catch (Throwable $ex) {
				// Log caught exception
				$this->logger->error(
					'An unhandled error occurred',
					[
						'source' => MetadataTypes\Sources\Module::UI->value,
						'type' => 'widgets-controller',
						'exception' => ApplicationHelpers\Logger::buildException($ex),
					],
				);

				throw new JsonApiExceptions\JsonApiError(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.base.messages.notUpdated.heading'),
					$this->translator->translate('//ui-module.base.messages.notUpdated.message'),
				);
			} finally {
				// Revert all changes when error occur
				if ($this->getOrmConnection()->isTransactionActive()) {
					$this->getOrmConnection()->rollBack();
				}
			}

			return $this->buildResponse($request, $response, $widget);
		}

		throw new JsonApiExceptions\JsonApiError(
			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
			$this->translator->translate('//ui-module.base.messages.invalidType.heading'),
			$this->translator->translate('//ui-module.base.messages.invalidType.message'),
			[
				'pointer' => '/data/type',
			],
		);
	}

	/**
	 * @throws ApplicationExceptions\InvalidState
	 * @throws Doctrine\DBAL\Exception
	 * @throws DoctrineOrmQueryExceptions\InvalidStateException
	 * @throws DoctrineOrmQueryExceptions\QueryException
	 * @throws Exceptions\InvalidState
	 * @throws Exceptions\Runtime
	 * @throws InvalidArgumentException
	 * @throws JsonApiExceptions\JsonApi
	 * @throws JsonApiExceptions\JsonApiError
	 */
	public function delete(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response,
	): Message\ResponseInterface
	{
		$widget = $this->findWidget(strval($request->getAttribute(Router\ApiRoutes::URL_ITEM_ID)));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$findDataSourcesQuery = new Queries\Entities\FindDataSources();
			$findDataSourcesQuery->forWidget($widget);

			foreach ($this->dataSourcesRepository->findAllBy($findDataSourcesQuery) as $dataSource) {
				$this->dataSourcesManager->delete($dataSource);
			}

			// Move widget back into warehouse
			$this->widgetsManager->delete($widget);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Log caught exception
			$this->logger->error(
				'An unhandled error occurred',
				[
					'source' => MetadataTypes\Sources\Module::UI->value,
					'type' => 'widgets-controller',
					'exception' => ApplicationHelpers\Logger::buildException($ex),
				],
			);

			throw new JsonApiExceptions\JsonApiError(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.base.messages.notDeleted.heading'),
				$this->translator->translate('//ui-module.base.messages.notDeleted.message'),
			);
		} finally {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}
		}

		return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
	}

	/**
	 * @throws Exception
	 * @throws DoctrineOrmQueryExceptions\InvalidStateException
	 * @throws DoctrineOrmQueryExceptions\QueryException
	 * @throws JsonApiExceptions\JsonApi
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		Message\ResponseInterface $response,
	): Message\ResponseInterface
	{
		$widget = $this->findWidget(strval($request->getAttribute(Router\ApiRoutes::URL_ITEM_ID)));

		$relationEntity = Utils\Strings::lower(strval($request->getAttribute(Router\ApiRoutes::RELATION_ENTITY)));

		if ($relationEntity === Schemas\Widgets\Widget::RELATIONSHIPS_DISPLAY) {
			return $this->buildResponse($request, $response, $widget->getDisplay());
		} elseif ($relationEntity === Schemas\Widgets\Widget::RELATIONSHIPS_DASHBOARDS) {
			$findDashboardsQuery = new Queries\Entities\FindDashboards();
			$findDashboardsQuery->forWidget($widget);

			return $this->buildResponse(
				$request,
				$response,
				$this->dashboardsRepository->findAllBy($findDashboardsQuery),
			);
		} elseif ($relationEntity === Schemas\Widgets\Widget::RELATIONSHIPS_GROUPS) {
			$findGroupsQuery = new Queries\Entities\FindGroups();
			$findGroupsQuery->forWidget($widget);

			return $this->buildResponse($request, $response, $this->groupsRepository->findAllBy($findGroupsQuery));
		} elseif ($relationEntity === Schemas\Widgets\Widget::RELATIONSHIPS_DATA_SOURCES) {
			$findDataSourcesQuery = new Queries\Entities\FindDataSources();
			$findDataSourcesQuery->forWidget($widget);

			return $this->buildResponse(
				$request,
				$response,
				$this->dataSourcesRepository->findAllBy($findDataSourcesQuery),
			);
		}

		return parent::readRelationship($request, $response);
	}

}
