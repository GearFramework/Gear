<?php

namespace gear\traits;

/**
 * Трэйт для добавления объектам базовых свойств и методов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TObject
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
     * Возвращает true, если указанное свойство существует
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isProperty(string $name): bool
    {
        return array_key_exists($name, $this->_properties);
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
    public function validate(string $name = '', $value = null, $default = null)
    {
        if (!$name) {
            foreach($this->props() as $name => $value) {
                $this->validate($name, $value, $default);
            }
        } else {
            if (self::$_validators) {
                foreach($this->getValidator() as $validator) {
                    if (!$name) {
                        $validator->validateObject($this);
                    } else if ($name && $value === null) {
                        $validator->validateProperty($this, $name, $default);
                    } else if ($name && $value !== null) {
                        $validator->validateValue($name, $value, $default);
                    }
                }
            }
        }
    }

    public function getValidator()
    {
        foreach(self::$_validators as $name => $validator) {
            if (is_string($validator)) {
                $validator = new $validator();
            }
            yield $validator;
        }
    }
}