<?php

use Gear\Library\GException;

/**
 * Базовые исключения ввода/вывода
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class IoException extends GException {}

/**
 * Базовые исключения файловой системы
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class FileSystemException extends IoException {}

/**
 * Базовые исключения директорий
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class DirectoryException extends FileSystemException {}

/**
 * Исключение, возникающее при отсутствии требуемой директории
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class DirectoryNotFoundException extends FileException {
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = "Directory <{directory}> not found";
}

/**
 * Базовые исключения файлов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class FileException extends FileSystemException {}

/**
 * Исключение, возникающее при отсутствии требуемого файла
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class FileNotFoundException extends FileException {
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = "File <{file}> not found";
}
