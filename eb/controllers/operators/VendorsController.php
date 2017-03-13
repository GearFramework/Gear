<?php

namespace eb\controllers\operators;

use eb\library\EbOperatorController;
use gear\Core;

/**
 * Контроллер менеджера операторов магазина
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class VendorsController extends EbOperatorController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_defaultApiName = 'list';
    protected $_layout = 'views/operators/operatorPage';
    protected $_viewPath = 'views/operators/home';
    protected $_caption = 'Управление магазином';
    /* Public */

    public function apiList()
    {
        return 'vendors';
    }
}
