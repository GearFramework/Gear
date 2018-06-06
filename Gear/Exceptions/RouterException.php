<?php

/**
 * Базовые исключения роутинга
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class RouterException extends \Gear\Library\GException {}

/**
 * Исключение, возникающее при передаче некорректного списка роутов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class InvalidRoutesException extends RouterException
{
    public $defaultMessage = "Invalid params to set in routes list";
}

/**
 * Исключение, возникающее, если не был найден контроллер по указанному роуту
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class ControllerClassIsEmptyException extends RouterException
{
    public $defaultMessage = "Controller class cannot resolved by route <{route}>";
}

/**
 * Исключение возникающее при передаче некорректного списка api-методов контроллера
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class InvalidApisException extends RouterException
{
    public $defaultMessage = 'Invalid params to set in api list';
}

/**
 * Исключение возникающее при отсутствии обязательных параметров api-методов в списке параметров запроса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class InvalidApiParamsException extends RouterException
{
    public $defaultMessage = 'Invalid api\'s params';
}
