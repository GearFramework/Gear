<?php

use \gear\library\GException;

/**
 * Класс исключения ядра фреймворка
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 01.08.2013
 */
class FileNotFoundException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = 'File :filename not found';
}
