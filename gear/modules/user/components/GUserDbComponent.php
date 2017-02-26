<?php

namespace gear\modules\user\components;

use gear\interfaces\IModel;
use gear\library\db\GDbStorageComponent;

class GUserDbComponent extends GDbStorageComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_factory = [
        'class' => '\gear\modules\user\models\GUser',
    ];
    protected static $_initialized = false;
    protected $_user = null;
    /* Public */

    public function getUser()
    {
        return $this->_user;
    }

    public function setUser($user)
    {
        $this->_user = $user;
    }
}
