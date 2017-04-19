<?php

namespace eb\controllers\operators;

use eb\library\EbOperatorController;
use gear\Core;
use gear\library\GTemplate;

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
    protected $_viewPath = 'views/operators/vendors';
    protected $_caption = 'Управление магазином';
    /* Public */

    public function apiList()
    {
        $template = new GTemplate(['bindsTemplates' => [
//            'data-navigator' => $this->_viewPath . '/vendorsNavigator',
//            'data-toolbar' => $this->_viewPath . '/vendorsToolbar',
            'data-content' => $this->_viewPath . '/vendorsList',
        ]]);
        return $this->view->render($template, ['vendors' => Core::vendors()->all()], true);
    }

    public function apiOrders(int $vendor)
    {
        $vendor = abs((int)$vendor);
        $vendor = Core::vendors()->byPk($vendor);
        $template = new GTemplate(['bindsTemplates' => [
            'data-navigator' => $this->_viewPath . '/vendorsNavigator',
            'data-toolbar' => $this->_viewPath . '/vendorsToolbar',
            'list-vendor-orders' => $this->_viewPath . '/vendorOrders',
        ]]);
        return $this->view->render($template, ['caption' => $vendor->name, 'vendor' => $vendor], true);
    }
}
