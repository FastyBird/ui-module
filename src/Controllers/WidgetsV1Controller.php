<?php declare(strict_types = 1);

/**
 * WidgetsV1Controller.php
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
use FastyBird\UIModule\Entities\Widgets\IAnalogActuator;
use FastyBird\UIModule\Entities\Widgets\IAnalogSensor;
use FastyBird\UIModule\Entities\Widgets\IDigitalActuator;
use FastyBird\UIModule\Entities\Widgets\IDigitalSensor;
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
 * API widgets controller
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class WidgetsV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TWidgetFinder;

	/** @var Hydrators\Widgets\AnalogActuatorWidgetHydrator */
	private Hydrators\Widgets\AnalogActuatorWidgetHydrator $analogActuatorHydrator;

	/** @var Hydrators\Widgets\AnalogSensorWidgetHydrator */
	private Hydrators\Widgets\AnalogSensorWidgetHydrator $analogSensorHydrator;

	/** @var Hydrators\Widgets\DigitalActuatorWidgetHydrator */
	private Hydrators\Widgets\DigitalActuatorWidgetHydrator $digitalActuatorHydrator;

	/** @var Hydrators\Widgets\DigitalSensorWidgetHydrator */
	private Hydrators\Widgets\DigitalSensorWidgetHydrator $digitalSensorHydrator;

	/** @var Models\Widgets\IWidgetRepository */
	private Models\Widgets\IWidgetRepository $widgetRepository;

	/** @var Models\Widgets\IWidgetsManager */
	private Models\Widgets\IWidgetsManager $widgetsManager;

	/**
	 * @param Models\Widgets\IWidgetRepository $widgetRepository
	 * @param Models\Widgets\IWidgetsManager $widgetsManager
	 * @param Hydrators\Widgets\AnalogActuatorWidgetHydrator $analogActuatorHydrator
	 * @param Hydrators\Widgets\AnalogSensorWidgetHydrator $analogSensorHydrator
	 * @param Hydrators\Widgets\DigitalActuatorWidgetHydrator $digitalActuatorHydrator
	 * @param Hydrators\Widgets\DigitalSensorWidgetHydrator $digitalSensorHydrator
	 */
	public function __construct(
		Models\Widgets\IWidgetRepository $widgetRepository,
		Models\Widgets\IWidgetsManager $widgetsManager,
		Hydrators\Widgets\AnalogActuatorWidgetHydrator $analogActuatorHydrator,
		Hydrators\Widgets\AnalogSensorWidgetHydrator $analogSensorHydrator,
		Hydrators\Widgets\DigitalActuatorWidgetHydrator $digitalActuatorHydrator,
		Hydrators\Widgets\DigitalSensorWidgetHydrator $digitalSensorHydrator
	) {
		$this->widgetRepository = $widgetRepository;
		$this->widgetsManager = $widgetsManager;
		$this->analogActuatorHydrator = $analogActuatorHydrator;
		$this->analogSensorHydrator = $analogSensorHydrator;
		$this->digitalActuatorHydrator = $digitalActuatorHydrator;
		$this->digitalSensorHydrator = $digitalSensorHydrator;
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
		$findQuery = new Queries\FindWidgetsQuery();

		$widgets = $this->widgetRepository->getResultSet($findQuery);

		// @phpstan-ignore-next-line
		return $this->buildResponse($request, $response, $widgets);
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
		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_ITEM_ID));

		return $this->buildResponse($request, $response, $widget);
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

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			switch ($document->getResource()->getType()) {
				case Schemas\Widgets\AnalogActuatorSchema::SCHEMA_TYPE:
					$widget = $this->widgetsManager->create($this->analogActuatorHydrator->hydrate($document));
					break;

				case Schemas\Widgets\DigitalActuatorSchema::SCHEMA_TYPE:
					$widget = $this->widgetsManager->create($this->digitalActuatorHydrator->hydrate($document));
					break;

				case Schemas\Widgets\AnalogSensorSchema::SCHEMA_TYPE:
					$widget = $this->widgetsManager->create($this->analogSensorHydrator->hydrate($document));
					break;

				case Schemas\Widgets\DigitalSensorSchema::SCHEMA_TYPE:
					$widget = $this->widgetsManager->create($this->digitalSensorHydrator->hydrate($document));
					break;

				default:
					throw new JsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//ui-module.widgets.messages.invalidType.heading'),
						$this->translator->translate('//ui-module.widgets.messages.invalidType.message'),
						[
							'pointer' => '/data/type',
						]
					);
			}

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
				'source'    => 'ui-module-widgets-controller',
				'type'      => 'create',
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.widgets.messages.notCreated.heading'),
				$this->translator->translate('//ui-module.widgets.messages.notCreated.message')
			);
		}

		$response = $this->buildResponse($request, $response, $widget);
		return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
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

		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if (
				$document->getResource()->getType() === Schemas\Widgets\AnalogActuatorSchema::SCHEMA_TYPE
				 && $widget instanceof IAnalogActuator
			) {
				$updateWidgetData = $this->analogActuatorHydrator->hydrate($document, $widget);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\DigitalActuatorSchema::SCHEMA_TYPE
				&& $widget instanceof IDigitalActuator
			) {
				$updateWidgetData = $this->digitalActuatorHydrator->hydrate($document, $widget);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\AnalogSensorSchema::SCHEMA_TYPE
				&& $widget instanceof IAnalogSensor
			) {
				$updateWidgetData = $this->analogSensorHydrator->hydrate($document, $widget);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\DigitalSensorSchema::SCHEMA_TYPE
				&& $widget instanceof IDigitalSensor
			) {
				$updateWidgetData = $this->digitalSensorHydrator->hydrate($document, $widget);

			} else {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.widgets.messages.invalidType.heading'),
					$this->translator->translate('//ui-module.widgets.messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			$widget = $this->widgetsManager->update($widget, $updateWidgetData);

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
				'source'    => 'ui-module-widgets-controller',
				'type'      => 'update',
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.widgets.messages.notUpdated.heading'),
				$this->translator->translate('//ui-module.widgets.messages.notUpdated.message')
			);
		}

		return $this->buildResponse($request, $response, $widget);
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
		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Move device back into warehouse
			$this->widgetsManager->delete($widget);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Log caught exception
			$this->logger->error('An unhandled error occurred', [
				'source'    => 'ui-module-widgets-controller',
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
				$this->translator->translate('//ui-module.widgets.messages.notDeleted.heading'),
				$this->translator->translate('//ui-module.widgets.messages.notDeleted.message')
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
		$widget = $this->findWidget($request->getAttribute(Router\Routes::URL_ITEM_ID));

		$relationEntity = strtolower($request->getAttribute(Router\Routes::RELATION_ENTITY));

		if ($relationEntity === Schemas\Widgets\WidgetSchema::RELATIONSHIPS_GROUPS) {
			return $this->buildResponse($request, $response, $widget->getGroups());

		} elseif ($relationEntity === Schemas\Widgets\WidgetSchema::RELATIONSHIPS_DISPLAY) {
			return $this->buildResponse($request, $response, $widget->getDisplay());

		} elseif ($relationEntity === Schemas\Widgets\WidgetSchema::RELATIONSHIPS_DATA_SOURCES) {
			return $this->buildResponse($request, $response, $widget->getDataSources());
		}

		return parent::readRelationship($request, $response);
	}

}
