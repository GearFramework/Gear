<?php

namespace Gear\Exceptions\Io\FileSystem;

use Gear\Interfaces\Io\IoExceptionInterface;
use Gear\Library\GearException;

/**
 * Исключение - файл не найден
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class FileNotFoundException extends GearException implements IoExceptionInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected string $defaultMessage = 'File <{filename}> not found';
    /* Public */
}
