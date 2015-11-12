<?php

namespace gear\traits;
use gear\Core;
use gear\library\GEvent;

/**
 * Трейт рендеринга шаблонов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 12.11.2015
 * @php 5.4.x or higher
 * @release 1.0.0
 */
trait TView
{
    protected $_arguments = [];

    public function setViewPath($path)
    {
        $this->_viewPath = $path;
        return $this;
    }

    public function getViewPath() { return $this->_viewPath; }

    /**
     * Отображение указанного представления
     *
     * @access public
     * @param string $view
     * @param array $arguments
     * @param bool $return
     * @return boolean|string
     */
    public function render($view = null, array $arguments = [], $return = false)
    {
        if (!$view)
            $view = $this->getOwner()->viewPath;
        else
        if (!preg_match('/[\/|\\\\]/', $view))
            $view = $this->getOwner()->viewPath . '\\' . $view;
        $viewPath = Core::resolvePath($view);
        if (!pathinfo($viewPath, PATHINFO_EXTENSION))
            $viewPath .= '.phtml';
        $resultRender = null;
        if ($this->trigger('onBeforeRender', new GEvent($this), $viewPath, $arguments))
        {
            $this->_arguments = $arguments;
            extract($arguments);
            if ($return)
            {
                if (Core::isComponentRegistered('configurator') && Core::c('configurator')->buffer)
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
            $this->trigger('onAfterRender', new GEvent($this), $resultRender);
        }
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
