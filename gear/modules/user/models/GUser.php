<?php

namespace gear\modules\user\models;

use gear\library\GModel;

/**
 * Модель пользователей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GUser extends GModel
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_primaryKey = 'id';
    /* Public */

    public function setSession($session)
    {
        $session->user = $this->{$this->primaryKey};
        $session->update();
    }
}
