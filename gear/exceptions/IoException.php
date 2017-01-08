<?php

use gear\library\GException;

/**
 * Базовые исключения ввода-вывода
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class IoException extends GException {}
class FileSystemException extends IoException {}
class DirectoryException extends FileSystemException {}
class FileException extends FileSystemException {}
class FileNotFoundException extends FileException {}
