<?php

namespace gear\components\resources;

use gear\Core;
use gear\library\GComponent;

/**
 * Менеджер публикации пользовательских ресурсов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GResourcesComponent extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Возвращает ссылку на опуликованный в html ресурс
     *
     * @param mixed $resource
     * @param array $options
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function publicate($resource, array $options = []): string
    {
        $resource = new GFile(['path' => Core::resolvePath($resource)]);
    }
}