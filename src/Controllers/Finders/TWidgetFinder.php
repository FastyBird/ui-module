<?php declare(strict_types = 1);

/**
 * TWidgetFinder.php
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

namespace FastyBird\UIModule\Controllers\Finders;

use FastyBird\JsonApi\Exceptions as JsonApiExceptions;
use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Models;
use FastyBird\UIModule\Queries;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Localization;
use Ramsey\Uuid;

/**
 * @property-read Localization\ITranslator $translator
 * @property-read Models\Widgets\IWidgetRepository $widgetRepository
 */
trait TWidgetFinder
{

	/**
	 * @param string $id
	 *
	 * @return Entities\Widgets\IWidget
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function findWidget(string $id): Entities\Widgets\IWidget
	{
		try {
			$findQuery = new Queries\FindWidgetsQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));

			$widget = $this->widgetRepository->findOneBy($findQuery);

			if ($widget === null) {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('//ui-module.base.messages.widgetNotFound.heading'),
					$this->translator->translate('//ui-module.base.messages.widgetNotFound.message')
				);
			}
		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//ui-module.base.messages.widgetNotFound.heading'),
				$this->translator->translate('//ui-module.base.messages.widgetNotFound.message')
			);
		}

		return $widget;
	}

}
