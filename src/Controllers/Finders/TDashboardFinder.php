<?php declare(strict_types = 1);

/**
 * TDashboardFinder.php
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
 * @property-read Models\Dashboards\IDashboardRepository $dashboardRepository
 */
trait TDashboardFinder
{

	/**
	 * @param string $id
	 *
	 * @return Entities\Dashboards\IDashboard
	 *
	 * @throws JsonApiExceptions\IJsonApiException
	 */
	protected function findDashboard(string $id): Entities\Dashboards\IDashboard
	{
		try {
			$findQuery = new Queries\FindDashboardsQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));

			$dashboard = $this->dashboardRepository->findOneBy($findQuery);

			if ($dashboard === null) {
				throw new JsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('//ui-module.base.messages.dashboardNotFound.heading'),
					$this->translator->translate('//ui-module.base.messages.dashboardNotFound.message')
				);
			}
		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//ui-module.base.messages.dashboardNotFound.heading'),
				$this->translator->translate('//ui-module.base.messages.dashboardNotFound.message')
			);
		}

		return $dashboard;
	}

}
