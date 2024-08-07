<?php declare(strict_types = 1);

/**
 * EntityParams.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           25.05.20
 */

namespace FastyBird\Module\Ui\Entities;

use Nette\Utils;

/**
 * Entity params field interface
 *
 * @package        FastyBird:UIModule!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface EntityParams
{

	/**
	 * @param array<string, mixed> $params
	 */
	public function setParams(array $params): void;

	public function getParams(): Utils\ArrayHash;

	public function setParam(string $key, mixed $value = null): void;

	public function getParam(string $key, mixed $default = null): mixed;

}
