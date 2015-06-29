<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GEvent;
use \gear\library\GException;

/** 
 * Базовый класс объектов. 
 * Все классы должны наследоваться от данного.
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.08.2013
 * @php 5.3.x
 */
class GObject
{
    /* Const */
    /* Private */
    /* Protected */

    /**
     * @var array of class configuration
     */
    protected static $_config = array
    (
        'plugins' => array
        (
            'view' => array('class' => '\gear\plugins\gear\GView'),
        ),
        'behaviors' => array(),
    );
    protected $_namespace = null;
    /**
     * @var int access level to object
     */
    protected $_access = Core::ACCESS_PUBLIC;
    /**
     * @var array of object properties
     */
    protected $_properties = array();
    /**
     * @var null|object owner object
     */
    protected $_owner = null;
    /**
     * @var array behaviors
     */
    protected $_behaviors = array();
    /**
     * @var array instances plugins
     */
    protected $_plugins = array();
    /**
     * @var array events handlers
     */
    protected $_events = array();
    /**
     * @var array array of plugin names, for loading on construct
     */
    protected $_preloads = array();
    /**
     * @var string path or namespace to object views
     */
    protected $_viewPath = 'views';
    /* Public */
    
    /**
     * Конструктор класса
     * Принимает ассоциативный массив свойств объекта и их значений
     * 
     * @access protected
     * @param array $properties
     * @param null|object $owner
     * @return GObject
     */
    protected function __construct(array $properties = array(), $owner = null)
    {
        if ($owner)
            $this->setOwner($owner);
        foreach($properties as $name => $value)
            $this->$name = $value;
        $this->event('onConstructed');
    }
    
    /**
     * Деструктор
     * Вызывает событие onDestroy
     *
     * @access public 
     * @return void
     */
    public function __destruct() { $this->event('onDestroy'); }
    
    /**
     * Запрещено клонирование
     * 
     * @access protected
     * @return void
     */
    protected function __clone() {}

    /**
     * В зависимости от значений параметров $name и $value может выполнять
     * действия:
     * - установка значения для свойства объекта через соответствующий сеттер
     * - установка обработчика события
     * - подключение поведения, если $value является анонимной функцией
     * - установка значения для свойства объекта, для которого не реализован сеттер
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
        if ($value instanceof \gear\interfaces\IPlugin)
            $this->installPlugin($name, $value);
        else
        if (is_callable($value) || $value instanceof \gear\interfaces\IBehavior)
            $this->attachBehavior($name, $value);
        else
            $this->_properties[$name] = $value;
    }
    
    /**
     * Если для свойства реализован геттер, то запускает его.
     * Если $name является названием события, то происходит вызов его
     * обработчиков
     * Если $name является поведением, то либо исполняет его, либо возвращает
     * инстанс класса поведения
     * Если $name является свойством объекта, то возвращает его значение
     * Во всех остальных случаях возвращается null
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
            return $this->event($name);
        else
        if ($this->isBehavior($name))
            return $this->b($name);
        else
        if ($this->isPluginRegistered($name))
            return $this->p($name);
        else
            return isset($this->_properties[$name]) ? $this->_properties[$name] : null;
    }
    
    /**
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
            return call_user_func_array(array($this, 'event'), $args);
        }
        if ($this->isBehavior($name))
            return call_user_func_array(array($this, 'b'), array_merge(array($name), $args));
        if ($this->isPluginRegistered($name))
        {
            $p = $this->p($name);
            if (!is_callable($p))
                $this->e('Plugin ":methodName" is not callable and cannot be use as function', array('methodName' => $name));
            return call_user_func_array($p, $args);
        }
        if (is_object($this->owner))
        {
            array_unshift($args, $this);
            return call_user_func_array(array($this->owner, $name), $args);
        }
        $result = $this->event('onCalled', $name, $args);
        if ($result === null)
            $this->e('Method ":methodName" is not exists', array('methodName' => $name));
        return $result;
    }
    
    /**
     * Генерация исключения на попытку вызова несуществующего статического 
     * метода
     * 
     * @access public
     * @param string $name
     * @param array $args
     * @return void
     */
    public static function __callStatic($name, $args)
    {
        static::e('Static method ":methodName" is not exists', ['methodName' => $name]);
    }
    
    /**
     * Возвращает true если $name является:
     * - событием, для которого имеются обработчики
     * - поведением
     * - свойством объекта
     * иначе возвращает false
     * 
     * @access public
     * @param string $name
     * @return mixed
     */
    public function __isset($name)
    {
        if ($this->isEvent($name))
            return true;
        else
        if ($this->isBehavior($name))
            return true;
        else
        if ($this->isPluginRegistered($name))
            return true;
        else
            return array_key_exists($name, $this->_properties);
    }
    
    /**
     * Метод производит удаление:
     * - обработчиков события, если $name таковым является
     * - отключает поведение, если $name является названием поведения
     * - удаляет свойство объекта, если таковое имеется
     * 
     * @access public
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        if ($this->isEvent($name))
            $this->detachEvent($name);
        else
        if ($this->isBehavior($name))
            $this->detachBehavior($name);
        else
        if (isset($this->_properties[$name]))
            unset($this->_properties[$name]);
    }
    
    /**
     * Возвращает имя класса объекта
     * 
     * @access public
     * @return string
     */
    public function __toString() { return get_class($this); }

    /**
     * Установка владельца объекта
     * 
     * @access public
     * @param object $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        if (!is_object($owner))
            $this->e('Owner must be object');
        $this->_owner = $owner;
        return $this;
    }
    
    /**
     * Получение владельца объекта
     * 
     * @acess public
     * @return object
     */
    public function getOwner() { return $this->_owner; }
    
    /**
     * Установка уровня доступа к объекту.
     * По-умолчанию метод принимает одно из значений: 
     * Core::ACCESS_PUBLIC = 2 - публичный 
     * Core::ACCESS_PROTECTED = 1 - защищённый и требует проверки прав доступа
     * Core::ACCESS_PRIVATE = 0 - доступ закрыт для всех кроме владельца $this->_owner
     * 
     * @access public
     * @param integer $access
     * @return $this
     * @see \gear\Core
     */
    public function setAccess($access)
    {
        $this->_access = $access;
        return $this;
    }
    
    /**
     * Возвращает уровень доступа к объекту
     * По-умолчанию метод принимает одно из значений: 
     * Core::ACCESS_PUBLIC|Core::ACCESS_PROTECTED|Core::ACCESS_PRIVATE
     * 
     * @access public
     * @return integer
     */
    public function getAccess() { return $this->_access; }
    
    /**
     * Установка пути к шаблонам отображения объекта
     * 
     * @access public
     * @param string $path
     * @return $this
     */
    public function setViewPath($path)
    {
        $this->_viewPath = $path;
        return $this;
    }

    /**
     * Взовращает путь к шаблонам отображения объекта
     * 
     * @access public
     * @return string
     */
    public function getViewPath() { return $this->_viewPath; }
    
    /**
     * Установка списка плагинов, которые необходимо инстанцировать
     * вместе с созданием объета. Экземпляры плагинов потом будут создаваться
     * в том порядке, в котором они расположены в списке
     * 
     * @access public
     * @param array $preloads
     * @return $this
     */
    public function setPreloads(array $preloads)
    {
        $this->_preloads = $preloads;
        return $this;
    }
    
    /**
     * Получение списка плагинов, требующих инстанцирования одновременно
     * с созданием объекта
     * 
     * @access public
     * @return array
     */
    public function getPreloads() { return $this->_preloads; }

    /**
     * Возвращает значение указанного конфигурационного параметра класса
     * 
     * @access public
     * @static
     * @param string $name
     * @return mixed
     */
    public static function i($name = null, $value = null)
    {
        if (is_null($name))
            return static::$_config;
        else
        if (is_null($value))
        {
            $value = null;
            if (isset(static::$_config[$name]))
                $value =  static::$_config[$name];
            else
            if (isset(self::$_config[$name]))
                $value = self::$_config[$name];
            return $value;
        }
        else
            static::$_config[$name] = $value;
    }
    
    /**
     * Возвращает набор поведений, описанных для данного класса
     * 
     * @access public
     * @return array
     */
    public function getBehaviors() { return $this->i('behaviors'); }

    /**
     * Возвращает true если объект имеет поведение с указанным названием, иначе
     * false
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function isBehavior($name) { return isset($this->_behaviors[$name]); }
    
    /**
     * Подключает к объекту набор поведений
     * 
     * @access public
     * @param array $behaviors
     * @return $this
     */
    public function attachBehaviors(array $behaviors)
    {
        foreach($behaviors as $name => $behavior)
            $this->attachBehavior($name, $behavior);
        return $this;
    }
    
    /**
     * Подключает поедение к объекту
     * 
     * @access public
     * @param string $name
     * @param string of class name|anonymous function $behavior
     * @return $this
     */
    public function attachBehavior($name, $behavior)
    {
        // $behavior is string of classname (class must be instance of \gear\interfaces\IBehavior)
        if (is_string($behavior))
            $this->_behaviors[$name] = $behavior::attach($this);
        else
        // $behavior is object instance of \Closure
        if ($behavior instanceof \Closure)
            $this->_behaviors[$name] = method_exists($behavior, 'bindTo') ? $behavior->bindTo($this, $this) : $behavior;
        else
        // $behavior is object instance of \gear\interfaces\IBehavior or is callable record
        // (release class::__invoke(), array('classname', 'methodname'))
        if ($behavior instanceof \gear\interfaces\IBehavior || is_callable($behavior))
            $this->_behaviors[$name] = $behavior->setOwner($this);
        else
            $this->e('Behavior ":behaviorName" is not correct', array('behaviorName' => $name));
        return $this;
    }
    
    /**
     * Если указанное поведение является анонимной функцией, то происходит
     * её выполнение с возвратом результата. Если поведение объект, то
     * возвращает его
     * Кроме параметра $name метод может принимать дополнительные
     * параметры, которые будут переданы в поведение, если он
     * является анонимной функцией
     * 
     * @access public
     * @param string $name
     * @return mixed
     */
    public function b($name)
    {
        if (!$this->isBehavior($name))
            $this->e('Behavior ":behaviorName" is not exists', array('behaviorName' => $name));
        $args = func_get_args();
        array_shift($args);
        return call_user_func_array($this->_behaviors[$name], $args);
    }
    
    /**
     * Отключает поведение объекта
     * 
     * @access public
     * @param string $name
     * @return $this
     */
    public function detachBehavior($name)
    {
        if (isset($this->_behaviors[$name]))
            unset($this->_behaviors[$name]);
        return $this;
    }
    
    /**
     * Возвращает true, если плагин зарегистрирован в текущем классе или
     * в родительском, иначе false 
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function isPluginRegistered($name)
    {
        return isset(static::$_config['plugins'][$name]) || isset(self::$_config['plugins'][$name]) || isset($this->_plugins[$name]);
    }
    
    /**
     * Возвращает инстанс плагина
     * 
     * @access public
     * @param string $name
     * @return GPlugin
     */
    public function p($name)
    {
        if (!isset($this->_plugins[$name]))
        {
            list($class, $config, $properties) = $this->getPluginRecord($name);
            if (!class_exists($class, false))
                $this->_plugins[$name] = $class::install($config, $properties, $this);
            else
                $this->_plugins[$name] = $class::it($properties, $this);
        }
        return $this->_plugins[$name];
    }
    
    /**
     * Возвращает массив описания плагина
     * [0] - название класса плагина
     * [1] - конфигурация класса плагина
     * [2] - свойства плагина
     * 
     * @access public
     * @param string $name
     * @return array
     */
    public function getPluginRecord($name)
    {
        if (isset(static::$_config['plugins'][$name]))
            $plugin = static::$_config['plugins'][$name];
        else
        if (isset(self::$_config['plugins'][$name]))
            $plugin = self::$_config['plugins'][$name];
        else
            $this->e('Plugin ":pluginName" is not registered', array('pluginName' => $name));
        return Core::getRecords($plugin);
    }
    
    /**
     * Установка плагина
     * 
     * @access public
     * @param string $name
     * @param object $instance
     * @return $this
     */
    public function installPlugin($name, $instance)
    {
        $this->_plugins[$name] = $instance;
        return $this;
    }
    
    /**
     * Возвращает true, если у объекта есть зарегистрированные обработчики 
     * указанного события, иначе false
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function isEvent($name)
    {
        return preg_match('/^on[A-Z]/', $name) && (method_exists($this, $name) || isset($this->_events[$name]));
    }
    
    /**
     * Добавление обработчика указанного события
     * 
     * @access public
     * @param string $name
     * @param callable mixed $handler
     * @return $this
     */
    public function attachEvent($name, $handler)
    {
        if (!is_callable($handler))
            $this->e('Incorrect handler of event ":eventName"', array('eventName' => $name));
        $this->_events[$name][] = $handler;
        return $this;
    }
    
    /**
     * Вызов обработчиков указанного события
     * 
     * @access public
     * @param string $name
     * @return mxied
     */
    public function event($name, $event = null)
    {
        $args = func_get_args();
        array_shift($args);
        if (is_null($event) || !($event instanceof \gear\library\GEvent))
        {
            $event = new GEvent($this);
            array_unshift($args, $event);
        }
        $result = method_exists($this, $name) ? call_user_func_array(array($this, $name), $args) : true;
        if (isset($this->_events[$name]) && $result)
        {
            foreach($this->_events[$name] as $handler)
            {
                $result = call_user_func_array($handler, $args);
                if (!$result)
                    break;
            }
        }
        return $result;
    }
    
    /**
     * Удаление обработчиков указанного события
     * Если указан параметр $handler, то будет удалён только один
     * конкретный обработчик
     * 
     * @access public
     * @param string $name
     * @param callable mixed $handler
     * @return $this
     */
    public function detachEvent($name, $handler = null)
    {
        if (isset($this->_events[$name]))
        {
            if (!$handler)
                unset($this->_events[$name]);
            else
            {
                foreach($this->_events[$name] as $index => $h)
                {
                    if ($h === $handler)
                        unset($this->_e[$name][$index]);
                }
            }
        }
        return $this;
    }

    /**
     * Может принимать до двух аргументов. Количество аргументов влияет на
     * поведение и возвращаемый результат.
     *
     * - Без аргументов возвращает ассоциативный массив свойств объекта
     * - 1 аргумент, если тип аргумента строковый, то предполагается, что
     *   передано название свойства объекта, значение которого необходимо
     *   получить; если передан массив, то:
     *   а. Индексированный массив - предполагается получить значения указанных
     *      свойств объекта
     *   б. Ассоциативный массив - предполагается, что необходимо для всех
     *      переданных названий свойств объекта установить соответствующие
     *      им значения, где ключ массива - название свойства, значение под
     *      ключём - его новое значение(в данном случае, метод возвращает $this)
     *   в. Смешанный массив - все нечисловые ключи предполагаются названиями
     *      свойств объекта, для которого необходимо установить значение,
     *      находящееся под соответствующим ключём. Все СТРОКОВЫЕ значения под
     *      числовыми ключами, предполагаются как названия свойств объекта,
     *      значения которых необходимо получить. В данном случае подведение
     *      метода - компиляция поведений пунктов а. и б.
     * - 2 аргумента, для свойства объекта, название которого берётся из
     *   первого аргумента, устанавливается значение из второго аргумента
     *   (в данном случае, метод возвращает $this)
     *
     * $this->props(); - вернёт ассоциативный массив всех свойств объекта
     * $this->props('name'); - вернёт значение свойства name
     * $this->props('name', 'value'); - установит значение value1 для
     *                                  свойства name
     * $this->props(array('name1', 'name2')); - вернёт массив из значений
     *                                          свойств name1 и name2
     * $this->props(array('name1' => 'value1', 'name2' => 'value2')); - установит
     *              значения value1 и value2 для свойств объекта name1 и name2
     *              соответственно
     * $this->props(array('name1' => 'value1', 'name2', 'name3')); - для свойства
     *              name1 установит значение value1 и вернёт массив значений
     *              свойств name2 и name3
     * $this->props(array())); - Очистит значения всех свойств объекта
     * $this->props(array('name1', 'name2'), 'value3') - для свойств name1 и name2
     *                                                   установит значение value3
     *
     * @access public
     * @return mixed
     */
    public function props()
    {
        $countArgs = func_num_args();
        if (!$countArgs)
            return $this instanceof \gear\interfaces\ISchema ? $this->getSchemaValues() : $this->_properties;
        else
        if ($countArgs === 1)
        {
            $args = func_get_args();
            $name = $args[0];
            if (is_array($name))
            {
                if (!count($name))
                {
                    $this->_properties = array_fill_keys(array_keys($this->_properties), null);
/*                    foreach($this->_properties as $name => &$value)
                        $value = null;
                    unset($value);*/
                    return $this;
                }
                else
                {
                    $requestProps = [];
                    foreach($name as $propName => $propValue)
                    {
                        if (is_numeric($propName))
                        {
                            if (is_string($propValue))
                                $requestProps[$propValue] = $this->props($propValue);
                        }
                        else
                        {
                            if (property_exists(get_class($this), $propName))
                                $this->$propName = $propValue;
                            else
                                $this->_properties[$propName] = $propValue;
                        }
                    }
                    return count($requestProps) ? $requestProps : $this;
                }
            }
            else
            {
                if (property_exists(get_class($this), $name))
                    return $this->$name;
                else
                    return isset($this->_properties[$name]) ? $this->_properties[$name] : null;
            }
        }
        else
        if ($countArgs === 2)
        {
            list($name, $value) = func_get_args();
            if (is_array($name))
            {
                $props = $name;
                foreach($props as $nameProp)
                {
                    if (property_exists(get_class($this), $nameProp))
                        $this->$nameProp = $value;
                    else
                        $this->_properties[$nameProp] = $value;
                }
            }
            else
            {
                if (property_exists(get_class($this), $name))
                    $this->$name = $value;
                else
                    $this->_properties[$name] = $value;
                return $this;
            }
        }
    }

    /**
     * Возвращает название пространства имён класса.
     *
     * @access public
     * @return string
     */
    public function getNamespace()
    {
        if (!$this->_namespace)
        {
            $class = get_class($this);
            $this->_namespace = substr($class, 0, strrpos($class, '\\'));

        }
        return $this->_namespace;
    }

    /**
     * Генерация исключений
     *
     * @access public
     * @param string $message
     * @return void
     */
    public static function e($message, $params = array(), $code = 0, \Exception $previous = null)
    {
        Core::e($message, $params, $code, $previous, get_called_class());
    }

    /**
     * Получение соответствующего класса исключения по названию класса
     *
     * @access protected
     * @static
     * @param string $class
     * @return string
     */
    protected static function _getExceptionClass($class)
    {
        $path = str_replace('\\', '/', $class);
        return str_replace('/', '\\', dirname($path) . '/' . substr(basename($path), 1) . 'Exception');
    }
    
    /**
     * Обработчик события, вызываемого на этапе создания объекта (из
     * конструктора)
     * 
     * @access public
     * @param GEvent $event
     * @return boolean
     */
    public function onConstructed()
    {
        $this->_preloading();
        $this->attachBehaviors($this->getBehaviors());
        return true;
    }
    
    /**
     * Презагрузка зависимых компонентов объекта
     * 
     * @access protected
     * @return void
     */
    protected function _preloading()
    {
        foreach($this->getPreloads('plugins') as $pluginName)
            $this->p($pluginName);
    }
}

/** 
 * Исключения базового класса объектов. 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
class ObjectException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
