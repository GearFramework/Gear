<?php

namespace gear\traits;

use gear\Core;


/**
 * Трейт для реализации динамических свойств объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TProperties
{
    /**
     * @var array $_defaultProperties значения по-умолчанию для объектов класса
     */
    protected static $_defaultProperties = [];
    protected static $_validators = [
        'object' => '\gear\validators\GObjectValidator',
    ];
    /**
     * @var array $_properties свойства объектов
     */
    protected $_properties = [];

    /**
     * генератор валидаторов объекта
     *
     * @return \Generator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getValidator()
    {
        foreach(static::$_validators as $name => $validator) {
            if (is_string($validator)) {
                $validator = new $validator();
                self::$_validators[$name] = $validator;
            } else if (is_array($validator) && !is_object(reset($validator))) {
                $method = null;
                if (count($validator) > 1) {
                    list($validator, $method) = $validator;
                }
                if (is_string($validator)) {
                    $validator = [new $validator, $method];
                } else {
                    list($class,, $properties) = Core::configure($validator);
                    $validator = [new $class($properties), $method];
                }
                self::$_validators[$name] = $validator;
            }
            yield $validator;
        }
    }

    /**
     * Возвращает true, если указанное свойство существует
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isProperty(string $name): bool
    {
        return array_key_exists($name, $this->_properties) || property_exists(get_class($this), $name);
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
     * @param mixed $name
     * @param mixed $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function props($name = null, $value = null)
    {
        $countArgs = func_num_args();
        if (!$countArgs) {
            return $this instanceof ISchema ? $this->getSchemaValues() : $this->_properties;
        } else if ($countArgs === 1) {
            $args = func_get_args();
            $name = $args[0];
            if (is_array($name)) {
                if (!count($name)) {
                    $this->_properties = array_fill_keys(array_keys($this->_properties), null);
                    return $this;
                } else {
                    $requestProps = [];
                    foreach ($name as $propName => $propValue) {
                        if (is_numeric($propName)) {
                            if (is_string($propValue))
                                $requestProps[$propValue] = $this->props($propValue);
                        } else {
                            if (property_exists(get_class($this), $propName))
                                $this->$propName = $propValue;
                            else
                                $this->_properties[$propName] = $propValue;
                        }
                    }
                    return count($requestProps) ? $requestProps : $this;
                }
            } else {
                if (property_exists(get_class($this), $name))
                    return $this->$name;
                else
                    return isset($this->_properties[$name]) ? $this->_properties[$name] : null;
            }
        } else if ($countArgs === 2) {
            list($name, $value) = func_get_args();
            if (is_array($name)) {
                $props = $name;
                foreach ($props as $nameProp) {
                    if (property_exists(get_class($this), $nameProp))
                        $this->$nameProp = $value;
                    else
                        $this->_properties[$nameProp] = $value;
                }
            } else {
                if (property_exists(get_class($this), $name))
                    $this->$name = $value;
                else {
                    $this->_properties[$name] = $value;
                }
                return $this;
            }
        }
    }

    /**
     * Восстановление дефолтных значений для свойств объекта
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function restoreDefaultProperties()
    {
        $default = static::i('objDefaultProperties') ?: [];
        $this->_properties = array_replace_recursive(static::$_defaultProperties, $default);
    }

    public function setValidators(array $validators)
    {
        static::$_validators = $validators;
    }

    /**
     * Валидация объекта или отдельных его свойств
     *
     * @param string $name
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function validate($name = null, $value = null, $default = null, $validator = null)
    {
        if ($validator) {
            if (is_callable($validator)) {
                return $validator($value);
            } else if (class_exists($validator)) {
                $validator = new $validator();
                return $validator->validateValue($value);
            }
        } else if (static::$_validators) {
            foreach($this->getValidator() as $validator) {
                if (is_array($validator)) {
                    list($validator, $method) = $validator;
                    if (!$name) {
                        $validator->$method($this);
                    }
                    $validator->$method($this);
                } else if (is_object($validator)) {
                    if (!$name) {
                        $validator->validateObject($this);
                    } else if ($name && $value === null) {
                        $validator->validateProperty($this, $name, $default);
                    } else if ($name && $value !== null) {
                        $validator->validateValue($value);
                    }
                }
            }
        }
    }
}
