<?php declare(strict_types = 1);

/**
 * DataSourcesV1Controller.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           27.05.20
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
 * API widgets display controller
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DataSourcesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TWidgetFinder;

	/** @var Models\Widgets\IWidgetRepository */
	protected Models\Widgets\IWidgetRepository $widgetRepository;

	/** @var Models\Widgets\DataSources\IDataSourceRepository */
	private Models\Widgets\DataSources\IDataSourceRepository $dataSourceRepository;

	/** @var Models\Widgets\DataSources\IDataSourcesManager */
	private Models\Widgets\DataSources\IDataSourcesManager $dataSourcesManager;

	/** @var Hydrators\Widgets\DataSources\ChannelPropertyDataSourceHydrator */
	private Hydrators\Widgets\DataSources\ChannelPropertyDataSourceHydrator $channelDataSourceHydrator;

	/**
	 * @param Models\Widgets\DataSources\IDataSourceRepository $dataSourceRepository
	 * @param Models\Widgets\DataSources\IDataSourcesManager $dataSourcesManager
	 * @param Models\Widgets\IWidgetRepository $widgetRepository
	 * @param Hydrators\Widgets\DataSources\ChannelPropertyDataSourceHydrator $channelDataSourceHydrator
	 */
	public function __construct(
		Models\Widgets\DataSources\IDataSourceRepository $dataSourceRepository,
		Models\Widgets\DataSources\IDataSourcesManager $dataSourcesManager,
		Models\Widgets\IWidgetRepository $widgetRepository,
		Hydrators\Widgets\DataSources\ChannelPropertyDataSourceHydrator $channelDataSourceHydrator
	) {
		$this->dataSourceRepository = $dataSourceRepository;
		$this->dataSourcesManager = $dataSourcesManager;
		$this->widgetRepository = $widgetRepository;
		$this->channelDataSourceHydrator = $channelDataSourceHydrator;
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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_WIDGET_ID));

		$findQuery = new Queries\FindDataSourcesQuery();
		$findQuery->forWidget($widget);

		$dataSources = $this->dataSourceRepository->getResultSet($findQuery);

		// @phpstan-ignore-next-line
		return $this->buildResponse($request, $response, $dataSources);
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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_WIDGET_ID));

		$dataSource = $this->findDataSource($request->getAttribute(Router\Routes::URL_ITEM_ID), $widget);

		return $this->buildResponse($request, $response, $dataSource);
	}

	/**
	 * @param string $id
	 * @param Entities\Widgets\IWidget $widget
	 *
	 * @return Entities\Widgets\DataSources\IDataSource
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	private function findDataSource(
		string $id,
		Entities\Widgets\IWidget $widget
	): Entities\Widgets\DataSources\IDataSource {
		try {
			$findQuery = new Queries\FindDataSourcesQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));
			$findQuery->forWidget($widget);

			$dataSource = $this->dataSourceRepository->findOneBy($findQuery);

			if ($dataSource === null) {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('//ui-module.dataSources.messages.notFound.heading'),
					$this->translator->translate('//ui-module.dataSources.messages.notFound.message')
				);
			}
		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//ui-module.dataSources.messages.notFound.heading'),
				$this->translator->translate('//ui-module.dataSources.messages.notFound.message')
			);
		}

		return $dataSource;
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
		// At first, try to load widget
		$this->findWidget($request->getAttribute(Router\Routes::URL_WIDGET_ID));

		$document = $this->createDocument($request);

		if ($document->getResource()->getType() === Schemas\Widgets\DataSources\ChannelPropertyDataSourceSchema::SCHEMA_TYPE) {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$dataSource = $this->dataSourcesManager->create($this->channelDataSourceHydrator->hydrate($document));

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
					'source'    => 'ui-module-data-sources-controller',
					'type'      => 'create',
					'exception' => [
						'message' => $ex->getMessage(),
						'code'    => $ex->getCode(),
					],
				]);

				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.dataSources.messages.notCreated.heading'),
					$this->translator->translate('//ui-module.dataSources.messages.notCreated.message')
				);
			}

			$response = $this->buildResponse($request, $response, $dataSource);
			return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
		}

		throw new JsonApiExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
			$this->translator->translate('//ui-module.dataSources.messages.invalidType.heading'),
			$this->translator->translate('//ui-module.dataSources.messages.invalidType.message'),
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

		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_WIDGET_ID));

		$dataSource = $this->findDataSource($request->getAttribute(Router\Routes::URL_ITEM_ID), $widget);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if (
				$document->getResource()->getType() === Schemas\Widgets\DataSources\ChannelPropertyDataSourceSchema::SCHEMA_TYPE
				&& $dataSource instanceof Entities\Widgets\DataSources\ChannelPropertyDataSource
			) {
				$updateDataSourceData = $this->channelDataSourceHydrator->hydrate($document, $dataSource);

			} else {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.dataSources.messages.invalidType.heading'),
					$this->translator->translate('//ui-module.dataSources.messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			$dataSource = $this->dataSourcesManager->update($dataSource, $updateDataSourceData);

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
				'source'    => 'ui-module-data-sources-controller',
				'type'      => 'update',
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.dataSources.messages.notUpdated.heading'),
				$this->translator->translate('//ui-module.dataSources.messages.notUpdated.message')
			);
		}

		return $this->buildResponse($request, $response, $dataSource);
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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_WIDGET_ID));

		$dataSource = $this->findDataSource($request->getAttribute(Router\Routes::URL_ITEM_ID), $widget);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Move device back into warehouse
			$this->dataSourcesManager->delete($dataSource);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Log caught exception
			$this->logger->error('An unhandled error occurred', [
				'source'    => 'ui-module-data-sources-controller',
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
				$this->translator->translate('//ui-module.dataSources.messages.notDeleted.heading'),
				$this->translator->translate('//ui-module.dataSources.messages.notDeleted.message')
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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_WIDGET_ID));

		$dataSource = $this->findDataSource($request->getAttribute(Router\Routes::URL_ITEM_ID), $widget);

		$relationEntity = strtolower($request->getAttribute(Router\Routes::RELATION_ENTITY));

		if ($relationEntity === Schemas\Widgets\DataSources\DataSourceSchema::RELATIONSHIPS_WIDGET) {
			return $this->buildResponse($request, $response, $dataSource->getWidget());
		}

		return parent::readRelationship($request, $response);
	}

}
