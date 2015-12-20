<?php

namespace gear\plugins\gear;

use gear\Core;
use gear\library\GPlugin;
use gear\library\GEvent;
use gear\traits\TView;

/**
 * Плагин, отвечающий за отображение представлений
 *
 * @package Gear Framework
 * @plugin View
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 02.08.2013
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GView extends GPlugin
{
    /* Traits */
    use TView;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    /* Public */

    /**
     * Отображение указанного представления
     *
     * @access public
     * @param string $view
     * @param array $arguments
     * @param bool $return
     * @return boolean|string
     * @see render()
     */
    public function __invoke($view = null, array $arguments = [], $return = false) {
        return $this->render($view, $arguments, $return);
    }
}
