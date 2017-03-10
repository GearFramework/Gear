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
        'sessionTimeLife' => [[
                'class' => '\gear\validators\GSessionValidator',
                'timeLife' => 900,
            ], 'validateTimeLife'
        ],
        'sessionToken' => [
            ['class' => '\gear\validators\GSessionValidator'], 'validateToken'
        ],
    ];
    protected $_maxTimeLife = 900;
    /* Public */

    /**
     * @return int
     */
    public function getMaxTimeLife()
    {
        return $this->_maxTimeLife;
    }

    public function getUser(): int
    {
        return $this->props('user');
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

    public function onAfterUpdate()
    {
        return true;
    }

    public function onBeforeUpdate()
    {
        $this->props('timeSession', date('Y-m-d H:i:s'));
        $this->token = $this->owner->createHash();
        return true;
    }

    public function setMaxTimeLife(int $time)
    {
        $this->_maxTimeLife = $time;
    }
}
