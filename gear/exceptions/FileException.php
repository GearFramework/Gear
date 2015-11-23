<?php

use \gear\library\GException;

/**
 * Классы исключений файлов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 14.08.2015
 * @release 1.0.0
 */
class FileException extends GException {}
class FileNotFoundException extends FileException { public $defaultMessage = 'File :filename not found'; }
