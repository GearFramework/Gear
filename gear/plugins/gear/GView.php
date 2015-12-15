<?php

namespace gear\plugins\gear;

use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GException;
use \gear\library\GEvent;

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
    public function __invoke($view = null, array $arguments = [], $return = false)
    {
        return $this->render($view, $arguments, $return);
    }

    /**
     * Отображение указанного представления
     *
     * @access public
     * @param string $view
     * @param array $arguments
     * @param bool|\Closure $return
     * @return boolean|string
     */
    public function render($view = null, array $arguments = [], $return = false)
    {
        Core::syslog(get_class($this) . ' -> Render ' . $view . ' [' . __LINE__ . ']');
        if (!$view)
            $view = $this->getOwner()->viewPath;
        else
        if (!preg_match('/[\/|\\\\]/', $view))
            $view = $this->owner->viewPath . '/' . $view;
        $viewPath = Core::resolvePath($view);
        if (!pathinfo($viewPath, PATHINFO_EXTENSION))
            $viewPath .= '.phtml';
        Core::syslog(get_class($this) . ' -> ' . $viewPath . ' [' . __LINE__ . ']');
        $this->trigger('onBeforeRender', new GEvent($this), $viewPath, $arguments);
        $this->_arguments = $arguments;
        extract($arguments);
        $resultRender = true;
        Core::syslog(get_class($this) . ' -> Require ' . $viewPath . ' [' . __LINE__ . ']');
        if ($return || $return instanceof \Closure) {
            if (Core::isComponentRegistered('configurator') && Core::c('configurator')->buffer) {
                $temp = ob_get_contents();
                ob_clean();
                require($viewPath);
                $resultRender = ob_get_contents();
                ob_clean();
                echo $temp;
            } else {
                ob_start();
                require($viewPath);
                $resultRender = ob_get_contents();
                ob_end_clean();
            }
            if ($return instanceof \Closure)
                $resultRender = $return($return);
        } else {
            require($viewPath);
        }
        $this->trigger('onAfterRender', new GEvent($this), $resultRender);
        return $resultRender;
    }

    /**
     * Получение значения указанного аргумента, переданного ранее в метод
     * отображения представления
     *
     * @access public
     * @param mixed $name
     * @return mixed
     */
    public function arg($name)
    {
        return isset($this->_arguments[$name]) ? $this->_arguments[$name] : null;
    }
}
