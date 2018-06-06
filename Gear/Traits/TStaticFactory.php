<?php

namespace Gear\Traits;

use Gear\Core;
use Gear\Interfaces\IObject;
use gear\library\GEvent;

/**
 * Методы статической фабрики объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TStaticFactory
{
    protected static $_factoryProperties = [
        'class' => '\Gear\Library\GModel'
    ];

    /**
     * @param IObject $object
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterFactory(IObject $object)
    {
        return $this->trigger('onAfterFactory', new GEvent($this, ['object' => $object]));
    }

    /**
     * @param array $properties
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeFactory(array $properties)
    {
        return $this->trigger('onBeforeFactory', new GEvent($this, ['properties' => $properties]));
    }

    /**
     * @param $properties
     * @param IObject|null $owner
     * @return IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function factory($properties, IObject $owner = null): ?IObject
    {
        if ($properties instanceof \Closure) {
            $properties = $properties($owner);
        }
        if (!is_array($properties)) {
            throw self::FactoryInvalidItemPropertiesException();
        }
        $object = null;
        if ($owner->beforeFactory($properties)) {
            $properties = array_replace_recursive(self::$_factoryProperties, $properties);
            list($class, $config, $properties) = Core::configure($properties);
            if (method_exists($class, 'install')) {
                $object = $class::install($config, $properties, $owner);
            } else {
                if ($config) {
                    $class::i($config);
                }
                $object = new $class($properties, $owner);
            }
            $owner->afterFactory($object);
        }
        return $object;
    }
}
