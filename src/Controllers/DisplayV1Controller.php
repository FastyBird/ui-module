<?php declare(strict_types = 1);

/**
 * DisplayV1Controller.php
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
use FastyBird\UIModule\Router;
use FastyBird\UIModule\Schemas;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message;
use Throwable;

/**
 * API widgets display controller
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DisplayV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TWidgetFinder;

	/** @var Models\Widgets\IWidgetRepository */
	protected Models\Widgets\IWidgetRepository $widgetRepository;

	/** @var Models\Widgets\Displays\IDisplaysManager */
	private Models\Widgets\Displays\IDisplaysManager $displaysManager;

	/** @var Hydrators\Widgets\Displays\AnalogValueHydrator */
	private Hydrators\Widgets\Displays\AnalogValueHydrator $analogValueHydrator;

	/** @var Hydrators\Widgets\Displays\ButtonHydrator */
	private Hydrators\Widgets\Displays\ButtonHydrator $buttonHydrator;

	/** @var Hydrators\Widgets\Displays\ChartGraphHydrator */
	private Hydrators\Widgets\Displays\ChartGraphHydrator $chartGraphHydrator;

	/** @var Hydrators\Widgets\Displays\DigitalValueHydrator */
	private Hydrators\Widgets\Displays\DigitalValueHydrator $digitalValueHydrator;

	/** @var Hydrators\Widgets\Displays\GaugeHydrator */
	private Hydrators\Widgets\Displays\GaugeHydrator $gaugeHydrator;

	/** @var Hydrators\Widgets\Displays\GroupedButtonHydrator */
	private Hydrators\Widgets\Displays\GroupedButtonHydrator $groupedButtonHydrator;

	/** @var Hydrators\Widgets\Displays\SliderHydrator */
	private Hydrators\Widgets\Displays\SliderHydrator $sliderHydrator;

	/**
	 * @param Models\Widgets\Displays\IDisplaysManager $displaysManager
	 * @param Models\Widgets\IWidgetRepository $widgetRepository
	 * @param Hydrators\Widgets\Displays\AnalogValueHydrator $analogValueHydrator
	 * @param Hydrators\Widgets\Displays\ButtonHydrator $buttonHydrator
	 * @param Hydrators\Widgets\Displays\ChartGraphHydrator $chartGraphHydrator
	 * @param Hydrators\Widgets\Displays\DigitalValueHydrator $digitalValueHydrator
	 * @param Hydrators\Widgets\Displays\GaugeHydrator $gaugeHydrator
	 * @param Hydrators\Widgets\Displays\GroupedButtonHydrator $groupedButtonHydrator
	 * @param Hydrators\Widgets\Displays\SliderHydrator $sliderHydrator
	 */
	public function __construct(
		Models\Widgets\Displays\IDisplaysManager $displaysManager,
		Models\Widgets\IWidgetRepository $widgetRepository,
		Hydrators\Widgets\Displays\AnalogValueHydrator $analogValueHydrator,
		Hydrators\Widgets\Displays\ButtonHydrator $buttonHydrator,
		Hydrators\Widgets\Displays\ChartGraphHydrator $chartGraphHydrator,
		Hydrators\Widgets\Displays\DigitalValueHydrator $digitalValueHydrator,
		Hydrators\Widgets\Displays\GaugeHydrator $gaugeHydrator,
		Hydrators\Widgets\Displays\GroupedButtonHydrator $groupedButtonHydrator,
		Hydrators\Widgets\Displays\SliderHydrator $sliderHydrator
	) {
		$this->displaysManager = $displaysManager;
		$this->widgetRepository = $widgetRepository;
		$this->analogValueHydrator = $analogValueHydrator;
		$this->buttonHydrator = $buttonHydrator;
		$this->chartGraphHydrator = $chartGraphHydrator;
		$this->digitalValueHydrator = $digitalValueHydrator;
		$this->gaugeHydrator = $gaugeHydrator;
		$this->groupedButtonHydrator = $groupedButtonHydrator;
		$this->sliderHydrator = $sliderHydrator;
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

		return $this->buildResponse($request, $response, $widget->getDisplay());
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

		$display = $widget->getDisplay();

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if (
				$document->getResource()->getType() === Schemas\Widgets\Display\AnalogValueSchema::SCHEMA_TYPE
				&& $display instanceof Entities\Widgets\Display\IAnalogValue
			) {
				$updateDisplayData = $this->analogValueHydrator->hydrate($document, $display);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\Display\ButtonSchema::SCHEMA_TYPE
				&& $display instanceof Entities\Widgets\Display\IButton
			) {
				$updateDisplayData = $this->buttonHydrator->hydrate($document, $display);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\Display\ChartGraphSchema::SCHEMA_TYPE
				&& $display instanceof Entities\Widgets\Display\IChartGraph
			) {
				$updateDisplayData = $this->chartGraphHydrator->hydrate($document, $display);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\Display\DigitalValueSchema::SCHEMA_TYPE
				&& $display instanceof Entities\Widgets\Display\IDigitalValue
			) {
				$updateDisplayData = $this->digitalValueHydrator->hydrate($document, $display);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\Display\GaugeSchema::SCHEMA_TYPE
				&& $display instanceof Entities\Widgets\Display\IGauge
			) {
				$updateDisplayData = $this->gaugeHydrator->hydrate($document, $display);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\Display\GroupedButtonSchema::SCHEMA_TYPE
				&& $display instanceof Entities\Widgets\Display\IGroupedButton
			) {
				$updateDisplayData = $this->groupedButtonHydrator->hydrate($document, $display);

			} elseif (
				$document->getResource()->getType() === Schemas\Widgets\Display\SliderSchema::SCHEMA_TYPE
				&& $display instanceof Entities\Widgets\Display\ISlider
			) {
				$updateDisplayData = $this->sliderHydrator->hydrate($document, $display);

			} else {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//ui-module.display.messages.invalidType.heading'),
					$this->translator->translate('//ui-module.display.messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			$display = $this->displaysManager->update($display, $updateDisplayData);

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
				'source'    => 'ui-module-display-controller',
				'type'      => 'update',
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//ui-module.display.messages.notUpdated.heading'),
				$this->translator->translate('//ui-module.display.messages.notUpdated.message')
			);
		}

		return $this->buildResponse($request, $response, $display);
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

		$relationEntity = strtolower($request->getAttribute(Router\Routes::RELATION_ENTITY));

		if ($relationEntity === Schemas\Widgets\Display\DisplaySchema::RELATIONSHIPS_WIDGET) {
			return $this->buildResponse($request, $response, $widget);
		}

		return parent::readRelationship($request, $response);
	}

}
