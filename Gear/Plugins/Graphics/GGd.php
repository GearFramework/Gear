<?php

namespace Gear\Plugins\Graphics;

use Gear\Library\GPlugin;


/**
 * Плагин для работы с графикой посреством GD
 *
 * @package Gear Framework
 *
 * @property null|resource resource
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class GGd extends GPlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_resource = null;
    /* Public */

    /**
     * Возвращает ресурс изображения
     *
     * @return resource|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Устанавливает ресурс изображения
     *
     * @param resource $resource
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function setResource($resource)
    {
        $this->_resource = $resource;
    }
}
