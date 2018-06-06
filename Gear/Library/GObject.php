<?php

namespace Gear\Library;

use Gear\Core;
use Gear\Interfaces\IObject;
use Gear\Traits\TEvent;
use Gear\Traits\TObject;
use Gear\Traits\TProperties;
use Gear\Traits\TView;

/**
 * Базовый класс объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @property IObject|null owner
 * @property array _events
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GObject implements IObject
{
    /* Traits */
    use TObject;
    use TProperties;
    use TEvent;
    use TView;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    /**
     * @var array $_config конфигурация класса
     */
    protected static $_config = [
        'plugins' => [
            'view' => ['class' => '\Gear\Plugins\Templater\GViewerPlugin']
        ]
    ];
    /**
     * @var array $_defaultProperties значения по-умолчанию для объектов класса
     */
    protected static $_sleepProperties = ['access', 'owner'];
    /**
     * @var int $_access режим доступа к объекту
     */
    protected $_access = Core::ACCESS_PUBLIC;
    /**
     * @var null|string пространство имён класса объекта
     */
    protected $_namespace = null;
    /* Public */

    /**
     * Клонирование объекта
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __clone() {}

    /**
     * Возвращает название класса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __toString(): string
    {
        return static::class;
    }

    /**
     * Обработка вызовов несуществующих статических методов класса
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (preg_match('/Exception$/', $name)) {
            array_unshift($arguments, $name);
            return Core::e(...$arguments);
        } elseif (preg_match('/^on[A-Z]/', $name)) {
            array_unshift($arguments, $name);
            return Core::trigger(...$arguments);
        }
        throw self::ObjectException('Static method <{methodName}> not exists', ['methodName' => $name]);
    }
}
