<?php

namespace gear\plugins\gear;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GException;

/** 
 * Плагин, отвечающий за отображение представлений
 * 
 * @package Gear Framework
 * @plugin View
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 02.08.2013
 */
class GView extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_arguments = array();
    /* Public */
    
    /**
     * Отображение указанного представления
     * 
     * @access public
     * @param string $view
     * @param array $arguments
     * @param bool $return
     * @return boolean|string
     */
    public function render($view = null, array $arguments = array(), $return = false)
    {
        $this->event('onBeforeRender');
        if ($view === null)
            $view = $this->getOwner()->getViewPath();
        $viewPath = Core::resolvePath($view);
        if (!pathinfo($viewPath, PATHINFO_EXTENSION))
            $viewPath .= '.phtml';
        $this->_arguments = $arguments;
        extract($arguments);
        $resultRender = true;
        if ($return)
        {
            if (Core::c('configurator')->buffer)
            {
                $temp = ob_get_contents();
                ob_clean();
                require($viewPath);
                $resultRender = ob_get_contents();
                ob_clean();
                echo $temp;
            }
            else
            {
                ob_start();
                require($viewPath);
                $resultRender = ob_get_contents();
                ob_end_clean();
            }
        }
        else
            require($viewPath);
        $this->event('onAfterRender', $resultRender);
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

/** 
 * Исключения плагина отображающего представления
 * 
 * @package Gear Framework
 * @plugin View
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 02.08.2013
 */
class ViewException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
