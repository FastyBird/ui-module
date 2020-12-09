<?php declare(strict_types = 1);

/**
 * DataSourcesV1Controller.php
 *
 * @license        More in license.md
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
use FastyBird\WebServer\Http as WebServerHttp;
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
	protected $widgetRepository;

	/** @var string */
	protected $translationDomain = 'module.dataSources';

	/** @var Models\Widgets\DataSources\IDataSourceRepository */
	private $dataSourceRepository;

	/** @var Models\Widgets\DataSources\IDataSourcesManager */
	private $dataSourcesManager;

	/** @var Hydrators\Widgets\DataSources\ChannelPropertyDataSourceHydrator */
	private $channelDataSourceHydrator;

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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Router::URL_WIDGET_ID));

		$findQuery = new Queries\FindDataSourcesQuery();
		$findQuery->forWidget($widget);

		$dataSources = $this->dataSourceRepository->getResultSet($findQuery);

		return $response
			->withEntity(WebServerHttp\ScalarEntity::from($dataSources));
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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Router::URL_WIDGET_ID));

		$dataSource = $this->findDataSource($request->getAttribute(Router\Router::URL_ITEM_ID), $widget);

		return $response
			->withEntity(WebServerHttp\ScalarEntity::from($dataSource));
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

		return $dataSource;
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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Router::URL_WIDGET_ID));

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
					$this->translator->translate('//module.base.messages.missingRequired.heading'),
					$this->translator->translate('//module.base.messages.missingRequired.message'),
					[
						'pointer' => $pointer,
					]
				);

			} catch (DoctrineCrudExceptions\EntityCreationException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//module.base.messages.missingRequired.heading'),
					$this->translator->translate('//module.base.messages.missingRequired.message'),
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
				->withEntity(WebServerHttp\ScalarEntity::from($dataSource))
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

		if ($request->getAttribute(Router\Router::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//module.base.messages.invalid.heading'),
				$this->translator->translate('//module.base.messages.invalid.message')
			);
		}

		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Router::URL_WIDGET_ID));

		$dataSource = $this->findDataSource($request->getAttribute(Router\Router::URL_ITEM_ID), $widget);

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
					$this->translator->translate('messages.invalidType.heading'),
					$this->translator->translate('messages.invalidType.message'),
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
			->withEntity(WebServerHttp\ScalarEntity::from($dataSource));
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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Router::URL_WIDGET_ID));

		$dataSource = $this->findDataSource($request->getAttribute(Router\Router::URL_ITEM_ID), $widget);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Move device back into warehouse
			$this->dataSourcesManager->delete($dataSource);

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
		// At first, try to load widget
		$widget = $this->findWidget($request->getAttribute(Router\Router::URL_WIDGET_ID));

		$dataSource = $this->findDataSource($request->getAttribute(Router\Router::URL_ITEM_ID), $widget);

		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Widgets\DataSources\DataSourceSchema::RELATIONSHIPS_WIDGET) {
			return $response
				->withEntity(WebServerHttp\ScalarEntity::from($dataSource->getWidget()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

}
