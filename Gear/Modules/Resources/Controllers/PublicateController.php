<?php

namespace Gear\Modules\Resources\Controllers;

use Gear\Core;
use Gear\Library\GController;

/**
 * Контроллер менеджера публикации пользовательских ресурсов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class PublicateController extends GController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Возвращает контент ресурса
     *
     * @param string $hash
     * @param string $type
     * @return mixed
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function apiGet(string $hash, string $type)
    {
        return Core::m('resources')->get($hash, $type, true);
    }
}
