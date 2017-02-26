<?php

namespace gear\modules\user\models;

use gear\library\GModel;

/**
 * Модель сессии пользователя
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GSession extends GModel
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_primaryKey = 'hash';
    /* Public */
}
