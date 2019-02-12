<?php

use Gear\Library\GException;

/**
 * Исключение, возникающее при ошибках во время установки компонента
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class ComponentInstallationIsInvalidException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}

/**
 * Исключение, которое возникает, когда вызванный компонент
 * не найден (не зарегистрирован и/или не установлен)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class ComponentNotFoundException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = "Component <{componentName}> not found";
}

/**
 * Исключение, возникающее при ошибках во время регистрации компонента
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class ComponentRegisteringIsInvalidException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}

/**
 * Исключение, возникающее при ошибках во время установки плагина
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class PluginInstallationIsInvalidException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}

/**
 * Исключение, которое возникает, когда вызванный плагин
 * не найден (не зарегистрирован и/или не установлен)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class PluginNotFoundException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = "Plugin <{pluginName}> not found";
}

/**
 * Исключение, возникающее при ошибках во время регистрации плагина
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class PluginRegisteringIsInvalidException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}

/**
 * Базовые исключения сервисов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class ServiceException extends GException {}

/**
 * Исключение при инициализации класса сервиса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class ServiceInitException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = "Error initializing service";
}

/**
 * Исключение при создании экземпляра сервиса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class ServiceConstructException extends ServiceException
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $defaultMessage = "Error creating service";
}
