<?php declare(strict_types = 1);

/**
 * InvalidStateException.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           25.05.20
 */

namespace FastyBird\UIModule\Exceptions;

use RuntimeException;

class InvalidStateException extends RuntimeException implements IException
{

}
