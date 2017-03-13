<?php

namespace eb\library;

use gear\Core;

abstract class EbClientController extends EbController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Возвращает текущий модуль управления операторами магазина
     *
     * @return \gear\interfaces\IModule
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getModule()
    {
        return Core::m('clients');
    }
}
