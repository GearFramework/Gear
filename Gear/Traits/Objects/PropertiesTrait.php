<?php

namespace Gear\Traits\Objects;

use Gear\Interfaces\Objects\SchemaInterface;

/**
 * Трейт для управления динамическими свойствами объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 *
 * @property array $properties
 */
trait PropertiesTrait
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

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
     * @param   null|string|array   $name
     * @param   mixed               $value
     * @return  mixed
     */
    public function props(null|string|array $name = null, mixed $value = null): mixed
    {
        $countArgs = func_num_args();
        if ($countArgs === 0) {
            return $this instanceof SchemaInterface ? $this->getSchemaValues() : $this->properties;
        }
        if ($countArgs === 1) {
            if (is_array($name)) {
                if (count($name) === 0) {
                    $this->properties = array_fill_keys(array_keys($this->properties), null);
                    return $this;
                }
                $requestProps = [];
                foreach ($name as $propName => $propValue) {
                    if (is_numeric($propName)) {
                        if (is_string($propValue)) {
                            $requestProps[$propValue] = $this->props($propValue);
                        }
                    } else {
                        property_exists(get_class($this), $propName)
                            ? $this->$propName = $propValue
                            : $this->properties[$propName] = $propValue;
                    }
                }
                return count($requestProps) ? $requestProps : $this;
            }
            if (property_exists($this, $name)) {
                return $this->$name;
            }
            return $this->properties[$name] ?? null;
        }
        if ($countArgs === 2) {
            list($name, $value) = func_get_args();
            if (is_array($name)) {
                $props = $name;
                foreach ($props as $nameProp) {
                    property_exists(get_class($this), $nameProp)
                        ? $this->$nameProp = $value
                        : $this->properties[$nameProp] = $value;
                }
            } else {
                property_exists(get_class($this), $name)
                    ? $this->$name = $value
                    : $this->properties[$name] = $value;
                return $this;
            }
        }
        return null;
    }

    /**
     * Возвращает true, если указанное свойство существует
     *
     * @param   string $name
     * @return  bool
     */
    public function isProperty(string $name): bool
    {
        return property_exists($this, $name)
            || array_key_exists($name, $this->properties);
    }
}
