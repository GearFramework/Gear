<?php

namespace gear\library;

use gear\interfaces\IModel;
use gear\interfaces\IObject;
use gear\traits\TBehaviorContained;
use gear\traits\TPluginContained;

/**
 * Базовый класс моделей
 *
 * @property \gear\interfaces\IObject|null owner
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GModel extends GObject implements IModel
{
    /* Traits */
    use TBehaviorContained;
    use TPluginContained;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * GObject constructor.
     *
     * @param array|\Closure $properties
     * @param \gear\interfaces\IObject|null $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __construct($properties = [], IObject $owner = null)
    {
        parent::__construct($properties, $owner);
    }

    /**
     * Клонирование объекта
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __clone()
    {
        parent::__clone();
    }

    /**
     * Возвращает спискок полей объекта для сериализации
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __sleep(): array
    {
        return parent::__sleep();
    }

    /**
     * Десериализация объекта
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __wakeup()
    {
        parent::__wakeup();
    }
}