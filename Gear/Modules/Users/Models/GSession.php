<?php

namespace Gear\Modules\Users\Models;

use Gear\Library\GModel;
use Gear\Modules\Users\Interfaces\SessionInterface;

/**
 * Базовая модель пользовательской сессии
 *
 * @package Gear Framework
 *
 * @property string hash
 * @property string lastTime
 * @property int user
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GSession extends GModel implements SessionInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public static $primaryKeyName = 'hash';
}
