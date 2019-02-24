<?php

namespace Gear\Traits\Db\Mysql;

use Gear\Interfaces\DbCursorInterface;

/**
 * Трейт для генерации курсора
 *
 * @package Gear Framework
 *
 * @property array cursorFactory
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
trait DbCursorFactoryTrait
{
    /**
     * Возвращает курсор
     *
     * @param array $properties
     * @return DbCursorInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getCursor(array $properties = []): DbCursorInterface
    {
        $properties = $this->getCursorFactory($properties);
        return $this->factory($properties, $this);
    }

    /**
     * Возвращает параметры создания курсора
     *
     * @param array $properties
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getCursorFactory(array $properties = []): array
    {
        return $properties ? array_replace_recursive($this->_cursorFactory, $properties) : $this->_cursorFactory;
    }

    /**
     * Установка параметров для создания курсора
     *
     * @param array|\Closure $cursorFactory
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function setCursorFactory($cursorFactory)
    {
        if ($cursorFactory instanceof \Closure) {
            $cursorFactory = $cursorFactory();
        }
        if (!is_array($cursorFactory)) {
            $cursorFactory = [];
        }
        $this->_cursorFactory = $cursorFactory;
    }
}
