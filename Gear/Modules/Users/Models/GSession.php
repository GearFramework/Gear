<?php

namespace Gear\Modules\Users\Models;

use Gear\Library\GModel;
use Gear\Modules\Users\Interfaces\ISession;

/**
 * Базовая модель пользовательской сессии
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GSession extends GModel implements ISession
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public static $primaryKeyName = 'hash';
}
