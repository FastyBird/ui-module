<?php declare(strict_types = 1);

/**
 * DashboardsManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Models\Dashboards;

use FastyBird\UIModule\Entities;
use FastyBird\UIModule\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Dashboards entities manager
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DashboardsManager implements IDashboardsManager
{

	use Nette\SmartObject;

	/** @var Crud\IEntityCrud */
	private Crud\IEntityCrud $entityCrud;

	public function __construct(
		Crud\IEntityCrud $entityCrud
	) {
		// Entity CRUD for handling entities
		$this->entityCrud = $entityCrud;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Dashboards\IDashboard {
		/** @var Entities\Dashboards\IDashboard $entity */
		$entity = $this->entityCrud->getEntityCreator()->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Dashboards\IDashboard $entity,
		Utils\ArrayHash $values
	): Entities\Dashboards\IDashboard {
		/** @var Entities\Dashboards\IDashboard $entity */
		$entity = $this->entityCrud->getEntityUpdater()->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Dashboards\IDashboard $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

}
