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
    protected static $_validators = [
        'sessionTimeLife' => [
            [
                'class' => '\gear\validators\GSessionValidator',
                'timeLife' => 900,
            ], 'validateTimeLife'
        ],
        'sessionToken' => [
            ['class' => '\gear\validators\GSessionValidator'], 'validateToken'
        ],
    ];
    protected $_timeLife = 900;
    /* Public */

    /**
     * @return int
     */
    public function getTimeLife()
    {
        return $this->_timeLife;
    }

    public function getUser(): int
    {
        return $this->user;
    }

    public function isValid(): bool
    {
        $result = true;
        try {
            $this->validate();
        } catch(\SessionException $e) {
            $result = false;
        }
        return $result;
    }

    public function setTimeLife(int $time)
    {
        $this->_timeLife = $time;
    }
}
