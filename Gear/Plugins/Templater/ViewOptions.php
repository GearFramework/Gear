<?php

namespace Gear\Plugins\Templater;

use Gear\Interfaces\Templater\ViewOptionsInterface;
use Gear\Traits\Objects\GetterTrait;
use Gear\Traits\Objects\SetterTrait;

/**
 * Класс опций отображения шаблонов
 *
 * @property bool $buffered
 * @property bool $useLayout
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class ViewOptions implements ViewOptionsInterface
{
    /* Traits */
    use GetterTrait;
    use SetterTrait;
    /* Const */
    /* Private */
    private bool $buffered = false;
    private bool $useLayout = true;
    /* Protected */
    /* Public */

    /**
     * Конструктор объекта
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        foreach ($properties as $nameProperty => $valueProperty) {
            $this->$nameProperty = $valueProperty;
        }
    }
}