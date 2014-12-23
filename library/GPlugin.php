<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GObject;
use \gear\library\GComponent;
use \gear\library\GException;
use \gear\interfaces\IPlugin;

/**
 * Класс описывающий плагин
 *
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.1.0
 * @since 01.08.2013
 */
abstract class GPlugin extends GComponent implements IPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array();
    protected static $_init = false;
    /* Public */

    /**
     * Метод, который выполняется во время инсталляции плагина.
     * Запускает инициализацию класса (конфигурирование) и возвращает
     * инстанс.
     *
     * @access public
     * @static
     * @param array|string as path to file $config
     * @param array $properties
     * @param null|object $owner
     * @return GPlugin
     */
    public static function install($config = [], array $properties = [], $owner = null)
    {
        static::checkDependency($owner);
        return parent::install($config, $properties, $owner);
    }

    /**
     * Проверка зависимости класса владельца
     *
     * @access public
     * @static
     * @param object $owner
     * @return boolean
     */
    public static function checkDependency($owner)
    {
        $dependencyClass = static::i('dependency');
        if (!(!$dependencyClass || ($dependencyClass && $owner instanceof $dependencyClass)))
            static::e('Owner has been instanced of ":ownerClass"', ['ownerClass' => $dependency]);
    }

    /**
     * Если есть сеттер, то устанавливает свойство через него.
     * Если название свойства начинается на "on", то вызывает метод
     * установки обработчика указанного в $name события
     * Устанавливает значение для собственного свойства
     *
     * @access public
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter))
            $this->$setter($value);
        else
        if (preg_match('/^on[A-Z]/', $name))
            $this->attachEvent($name, $value);
        else
            $this->_properties[$name] = $value;
    }

    /**
     * Если у свойства есть геттер, то вызвывает его.
     * Если название свойства начинается на "on", то вызывает
     * собтвенные обработчики события и своего владельца.
     * Если свойство присутствует в $_properties, то возвращает его значение,
     * иначе перенаправляет на владельца
     *
     * @access public
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter))
            return $this->$getter();
        else
        if (preg_match('/^on[A-Z]/', $name))
            return ($result = $this->event($name)) ? $this->_owner->event($name) : $result;
        else
            return array_key_exists($name, $this->_properties) ? $this->_properties[$name] : $this->getOwner()->$name;
    }

    /**
     * Вызывает обработчиков события как самого плагина, так и владельца,
     * если название метода начинается на "on", иначе перенаправляет
     * вызов метода на своего владельца
     *
     * @access public
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (preg_match('/^on[A-Z]/', $name))
        {
            array_unshift($args, $name);
            $result = call_user_func_array([$this, 'event'], $args);
            return $result ? call_user_func_array([$this->_owner, 'event'], $args) : $result;
        }
        return call_user_func_array([$this->getOwner(), $name], $args);
    }

    /**
     * Вызов плагина владельца
     *
     * @access public
     * @param string $name
     * @return object
     */
    public function p($name)
    {
        return $this->getOwner()->p($name);
    }

    /**
     * Обработчик события onConstructed, вызываемого после создания
     * экземпляра плагина
     *
     * @access public
     * @return void
     */
    public function eventConstructed()
    {
        return $this->event('onConstructed');
    }
}

/**
 * Класс Исключений плагина
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class PluginException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
