<?php

namespace Gear\Library\Objects;

use Gear\Interfaces\Objects\EntityInterface;
use Gear\Interfaces\Objects\ModelInterface;
use Gear\Interfaces\Services\PluginContainedInterface;
use Gear\Interfaces\Templater\ViewableInterface;
use Gear\Plugins\Templater\Viewer;
use Gear\Traits\Objects\GetterTrait;
use Gear\Traits\Objects\ModelTrait;
use Gear\Traits\Objects\PropertiesTrait;
use Gear\Traits\Objects\SetterTrait;
use Gear\Traits\Objects\ViewableTrait;
use Gear\Traits\Services\PluginContainedTrait;
use Stringable;

/**
 * Базовый класс моделей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Model implements ModelInterface, PluginContainedInterface, Stringable, ViewableInterface
{
    /* Traits */
    use ModelTrait;
    use GetterTrait;
    use SetterTrait;
    use PropertiesTrait;
    use PluginContainedTrait;
    use ViewableTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected static array $config = [
        'plugins' => [
            'view' => ['class' => Viewer::class],
        ],
    ];
    protected ?ModelInterface $owner = null;
    protected array $properties = [];
    protected array $renderSchemas = [];
    /* название плагина выступающего в качестве шаблонизатора */
    protected string $viewerName = 'view';
    /* Public */

    /**
     * Конструктор объекта. На вход принимает массив свойств и объект, который является владельцем
     * данного объекта
     *
     * @param array                 $properties
     * @param EntityInterface|null  $owner
     */
    protected function __construct(array $properties = [], ?EntityInterface $owner = null)
    {
        $this->beforeConstruct($properties, $owner);
        $this->setOwner($owner);
        foreach ($properties as $nameProperty => $valueProperty) {
            $this->$nameProperty = $valueProperty;
        }
        $this->afterConstruct();
    }

    /**
     * Возвращает название класса
     *
     * @return string
     */
    public function __toString(): string
    {
        return static::class;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->props();
    }

    /**
     * Выполняется до создания объекта
     *
     * @param   array                 $properties
     * @param   EntityInterface|null  $owner
     * @return  void
     */
    public function beforeConstruct(array &$properties, ?EntityInterface $owner): void {}

    /**
     * Выполняется после создание объекта
     *
     * @return void
     */
    public function afterConstruct(): void {}
}
