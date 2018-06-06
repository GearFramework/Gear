<?php

namespace Gear\Library;

use Gear\Interfaces\IModel;
use Gear\Interfaces\IObject;
use Gear\Traits\TPluginContained;

/**
 * Базовый класс моделей
 *
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
    use TPluginContained;
    /* Const */
    /* Private */
    /* Protected */
    protected $_primaryKeyName = 'id';
    /* Public */

    /**
     * GModel constructor.
     *
     * @param array|\Closure $properties
     * @param null|IObject $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __construct($properties = [], IObject $owner = null)
    {
        parent::__construct($properties, $owner);
    }

    /**
     * Возвращает значение поля, которое является первичным ключом
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPrimaryKey(): string
    {
        return $this->props($this->primaryKeyName);
    }

    /**
     * Возвращает название поля, которое является первичным ключом
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPrimaryKeyName(): string
    {
        return $this->_primaryKeyName;
    }

    /**
     * Устанавливает значение для поля, которое является первичным ключом
     *
     * @param mixed $value
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setPrimaryKey($value)
    {
        return $this->props($this->primaryKeyName, $value);
    }

    /**
     * Устанавливает название поля, которое является первичным ключом
     *
     * @param string $pkName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setPrimaryKeyName(string $pkName)
    {
        $this->_primaryKeyName = $pkName;
    }
}