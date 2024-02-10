<?php

namespace Gear\Library;

use Gear\Interfaces\ModelInterface;
use Gear\Interfaces\ObjectInterface;
use Gear\Traits\ModelTrait;

/**
 * Базовый класс моделей
 *
 * @package Gear Framework
 *
 * @property iterable plugins
 * @property mixed primaryKey
 * @property string primaryKeyName
 * @property iterable registeredPlugins
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class Model extends Object implements ModelInterface
{
    /* Traits */
    use ModelTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public static string $primaryKeyName = 'id';

    /**
     * Model constructor.
     *
     * @param array $properties
     * @param null|ObjectInterface $owner
     * @since 0.0.1
     * @version 2.0.0
     */
    public function __construct(array $properties = [], ?ObjectInterface $owner = null)
    {
        parent::__construct($properties, $owner);
    }
}
