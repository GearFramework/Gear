<?php

use \gear\Core;
use \gear\library\GException;

/**
* Исключения компонентов, работающих с базами данных
*
* @package Gear Framework
* @author Kukushkin Denis
* @copyright Kukushkin Denis
* @version 1.0.0
* @since 16.08.2015
*/
class DbException extends GException {}
class DbConnectionException extends DbException {}
class DbDatabaseException extends DbException {}
class DbCollectionException extends DbException {}
class DbCursorQueryError extends DbException
{
    public $viewPath = array
    (
        Core::HTTP => '\gear\views\db\exceptionHttp.html',
        Core::CLI => '\gear\views\db\exceptionConsole.html'
    );
}
class DbComponentNotFound extends DbException { public $defaultMessage = 'Db component :dbComponent not found'; }
