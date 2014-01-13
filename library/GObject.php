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
 * @version 0.0.1
 * @since 01.08.2013
 */
class GObject
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'plugins' => array
        (
            'view' => array('class' => '\\gear\\plugins\\gear\\GView'),
            'log' => array('class' => '\\gear\\plugins\\gear\\GLog'),
        ),
        'behaviors' => array
        (
        ),
    );
    protected $_properties = array();
    protected $_owner = null;
    protected $_behaviors = array();
    protected $_plugins = array();
    protected $_events = array();
    protected $_preloads = array();
    /* Public */
    public $viewPath = 'views';
    
    /**
     * Конструктор класса
     * Принимает ассоциативный массив свойств объекта и их значений
     * 
     * @access protected
     * @param array $properties
     * @return void
     */
    protected function __construct(array $properties = array())
    {
        foreach($properties as $name => $value)
            $this->$name = $value;
        $this->event('onConstructed');
    }
    
    /**
     * Деструктор
     * Вызывает событие onDestory
     *
     * @access public 
     * @return void
     */
    public function __destruct()
    {
        $this->event('onDestroy');
    }
    
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
        if (is_callable($value))
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
    
    public function __call($name, $args)
    {
        if (preg_match('/^on[A-Z]/', $name))
        {
            array_unshift($args, $name);
            return call_user_func_array(array($this, 'event'), $args);
        }
        if (isset($this->_behaviors[$name]) && is_callable($this->_behaviors[$name]))
            return call_user_func_array($this->_behaviors[$name], $args);
        else
        {
            foreach($this->_behaviors as $b)
            {
                if (!($b instanceof Closure) && method_exists($b, $name))
                    return call_user_func_array(array($b, $name), $args);
            }
        }
        if ($this->isPluginRegistered($name))
        {
            $p = $this->p($name);
            if (is_callable($p))
                return call_user_func_array($p, $args);
        }
        if (is_object($this->_owner))
        {
            array_unshift($args, $this);
            return call_user_func_array(array($this->_owner, $name), $args);
        }
        $this->e('Метод ":methodName" не реализован', array('methodName' => $name));
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
        $this->e('Метод ":methodName" не реализован', array('methodName' => $name));
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
     * @return boolen
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
            return isset($this->_properties[$name]);
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
    public function __toString()
    {
        return get_class($this);
    }

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
            $this->e('Владелец должен быть объектом');
        $this->_owner = $owner;
        return $this;
    }
    
    /**
     * Получение владельца объекта
     * 
     * @acess public
     * @return object
     */
    public function getOwner()
    {
        return $this->_owner;
    }

    /**
     * Возвращает значение указанного конфигурационного параметра класса
     * 
     * @access public
     * @static
     * @param string $name
     * @return mixed
     */
    public static function i($name = null)
    {
        if (is_null($name))
            return static::$_config;
        else
        if (isset(static::$_config[$name]))
            return static::$_config[$name];
        else
        if (isset(self::$_config[$name]))
            return self::$_config[$name];
        else
            return null;
    }

    /**
     * Регистрация поведений объекта
     * Array
     * (
     *      [name] => класс или анонимная функция,
     *      ...
     * )
     * 
     * @access public
     * @param array $behaviors
     * @return $this
     */
    public function setBehaviors(array $behaviors)
    {
        static::$_config['behaviors'] = array_replace_recursive(static::$_config['behaviors'], $behaviors);
        return $this;
    }
    
    /**
     * Получение списка зарегистрированных поведений
     * 
     * @access public
     * @return array
     */
    public function getBehaviors()
    {
        $behaviors = static::i('behaviors');
        return $behaviors ? $behaviors : array();
    }
    
    /**
     * Возвращает true если объект имеет поведение с указанным названием, иначе
     * false
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function isBehavior($name)
    {
        return isset($this->_behaviors[$name]) || isset(static::$_config['behaviors'][$name]) || isset(self::$_config['behaviors'][$name]);
    }
    
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
        // $behavior is object instance of \gear\interfaces\IBehavior or is callable record (release class::__invoke(), array('classname', 'methodname'))
        if ($behavior instanceof \gear\interfaces\IBehavior || is_callable($behavior))
            $this->_behaviors[$name] = $behavior->setOwner($this);
        else
        // $behavior is object instance of \Closure
        if ($behavior instanceof \Closure)
            $this->_behaviors[$name] = $behavior->bindTo($this, $this);
        else
            $this->e('Подключаемое поведение ":behaviorName" не является корректным', array('behaviorName' => $name));
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
        $args = func_get_args();
        array_shift($args);
        if (!$this->isBehavior($name))
            $this->e('Поведение ":behaviorName" не реализовано', array('behaviorName' => $name));
        if (isset($this->_behaviors[$name]))
        {
            if ($this->_behaviors[$name] instanceof Closure)
                return call_user_func_array($this->_behaviors[$name], $args);
            else
                return $this->_behaviors[$name];
        }
        else
        {
            $behaviors = $this->getBehaviors();
            if (!isset($behaviors[$name]))
                $this->e('Поведение ":behaviorName" не реализовано', array('behaviorName' => $name));
            $this->attachBehavior($name, $behaviors[$name]);
            return $this->b('name');
        }
            
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
     * Установка списка плагинов, которые необходимо инстанцировать
     * вместе с созданием объета. Экземпляры плагинов потом будут создаваться
     * в том порядке, в котором они расположены в списке
     * 
     * @access public
     * @param array $preloads
     * @return void
     */
    public function setPreloads(array $preloads)
    {
        $this->_preloads = $preloads;
    }
    
    /**
     * Получение списка плагинов, требующих инстанцирования одновременно
     * с созданием объекта
     * 
     * @access public
     * @return array
     */
    public function getPreloads()
    {
        return $this->_preloads;
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
        return isset(static::$_config['plugins'][$name]) || isset(self::$_config['plugins'][$name]);
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
            $this->_plugins[$name] = !class_exists($class, false) ? $class::install($config, $properties, $this) : $class::it($properties, $this);
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
            $this->e('Плагин ":pluginName" не зарегистрирован', array('pluginName' => $name));
        $class = $plugin['class'];
        unset($plugin['class']);
        $config = array();
        if (is_array($class))
        {
            $config = $class;
            $class = $config['name'];
            unset($config['name']);
        }
        return array($class, $config, $plugin);
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
            $this->e('Некорректный обработчик события ":eventName"', array('eventName' => $name));
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
    public function event($name)
    {
        $args = func_get_args();
        $args[0] = new GEvent($this);
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
        {
            return $this instanceof \gear\interfaces\ISchema
                   ? $this->getSchemaValues()
                   : $this->_properties;
        }
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
                    $requestProps = array();
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

    public function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * Генерация исключений
     * 
     * @access public
     * @param string $message
     * @param array $params
     * @param integer $type
     * @return void
     */
    public static function e($message, array $params = array(), $type = 0)
    {
        $class = get_called_class();
        $classException = $class . 'Exception';
        if (!class_exists($classException, false))
        {
            $classException = static::_getExceptionClass($class);
            if (!class_exists($classException, false))
                $classException = static::_getExceptionClass(get_parent_class($class));
        }
        $exception = class_exists($classException, false) 
                     ? new $classException($message, $params, $type) 
                     : new \gear\library\ObjectException($message, $params, $type);
        throw $exception;
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
        foreach($this->getPreloads() as $pluginName)
            $this->p($pluginName);
        $this->attachBehaviors($this->getBehaviors());
        return true;
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
