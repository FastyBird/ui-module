<?php declare(strict_types = 1);

/**
 * Manager.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 * @since          1.0.0
 *
 * @date           25.05.20
 */

namespace FastyBird\Module\Ui\Models\Entities\Widgets;

use FastyBird\Module\Ui\Entities;
use FastyBird\Module\Ui\Events;
use FastyBird\Module\Ui\Models;
use IPub\DoctrineCrud\Crud as DoctrineCrudCrud;
use IPub\DoctrineCrud\Exceptions as DoctrineCrudExceptions;
use Nette;
use Nette\Utils;
use Psr\EventDispatcher;
use function assert;

/**
 * Widgets entities manager
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Manager
{

	use Nette\SmartObject;

	/** @var DoctrineCrudCrud\IEntityCrud<Entities\Widgets\Widget>|null */
	private DoctrineCrudCrud\IEntityCrud|null $entityCrud = null;

	/**
	 * @param DoctrineCrudCrud\IEntityCrudFactory<Entities\Widgets\Widget> $entityCrudFactory
	 */
	public function __construct(
		private readonly DoctrineCrudCrud\IEntityCrudFactory $entityCrudFactory,
		private readonly EventDispatcher\EventDispatcherInterface|null $dispatcher = null,
	)
	{
	}

	public function create(Utils\ArrayHash $values): Entities\Widgets\Widget
	{
		$entity = $this->getEntityCrud()->getEntityCreator()->create($values);
		assert($entity instanceof Entities\Widgets\Widget);

		$this->dispatcher?->dispatch(new Events\EntityCreated($entity));

		return $entity;
	}

	/**
	 * @throws DoctrineCrudExceptions\InvalidArgumentException
	 */
	public function update(
		Entities\Widgets\Widget $entity,
		Utils\ArrayHash $values,
	): Entities\Widgets\Widget
	{
		$entity = $this->getEntityCrud()->getEntityUpdater()->update($values, $entity);
		assert($entity instanceof Entities\Widgets\Widget);

		$this->dispatcher?->dispatch(new Events\EntityUpdated($entity));

		return $entity;
	}

	/**
	 * @throws DoctrineCrudExceptions\InvalidArgumentException
	 */
	public function delete(Entities\Widgets\Widget $entity): bool
	{
		// Delete entity from database
		$result = $this->getEntityCrud()->getEntityDeleter()->delete($entity);

		if ($result) {
			$this->dispatcher?->dispatch(new Events\EntityDeleted($entity));
		}

		return $result;
	}

	/**
	 * @return DoctrineCrudCrud\IEntityCrud<Entities\Widgets\Widget>
	 */
	public function getEntityCrud(): DoctrineCrudCrud\IEntityCrud
	{
		if ($this->entityCrud === null) {
			$this->entityCrud = $this->entityCrudFactory->create(Entities\Widgets\Widget::class);
		}

		return $this->entityCrud;
	}

}
