<?php

namespace gear\library;
use gear\Core;
use gear\library\GModule;
use gear\library\GException;
use gear\library\GEvent;

/** 
 * Класс описывающий приложение
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GApplication extends GModule
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'components' => array
        (
            'process' => array
            (
                'class' => array
                (
                    'name' => '\gear\components\gear\process\GProcessComponent',
                    'defaultProcess' => 'index',
                ),
            ),
        ),
        'plugins' => array
        (
            'request' => array('class' => '\gear\plugins\gear\GRequest'),
            'env' => array('class' => '\gear\plugins\gear\GEnvironment'),
            'log' => array('class' => '\gear\plugins\gear\GLog'),
        ),
    );
    protected $_namespace = null;
    /* Public */
    
    /**
     * Запуск приложения
     * 
     * @access public
     * @return void
     */
    public function run($process = null, $request = null)
    {
        $args = func_get_args();
        if (!func_num_args())
            $args = array($this->request->isPost() ? $this->request->post() : $this->request->get());
        else
            $args = func_get_args();
        if ($this->event('onBeforeRun', $args))
        {
            $result = call_user_func_array(array($this->c('process'), 'exec'), $args);
            $this->event('onAfterRun', new GEvent($this), $result);
        }
    }

    /**
     * Возвращает название пространства имён приложения.
     * По сути это название папки, в которой находится класс приложения. 
     * Т.е. если файл класса приложения Test Находится по пути
     * /usr/share/gear/test/Test.php то данный метод приложения вернёт значение
     * test
     * 
     * @access public
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace ? $this->_namespace : $this->_namespace = basename(dirname(str_replace('\\', '/', get_class($this))));
    }

    /**
     * Возвращает либо Core::HTTP, если приложение запущено из браузера, либо
     * Core::CLI, если приложение запущено из консоли.
     * 
     * @access public
     * @see Core::HTTP
     * @see Core::CLI
     * @see Core::getMode()
     * @return integer
     */
    public function getMode() { return Core::getMode(); }
    
    /**
     * Возвращает true, если приложение запущено из браузера
     * 
     * @access public
     * @return bool
     */
    public function hasHttp() { return $this->getMode() === Core::HTTP; }
    
    /**
     * Возвращает true, если приложение запущено из консоли
     * 
     * @access public
     * @return bool
     */
    public function hasCli() { return $this->getMode() === Core::CLI; }
    
    /**
     * Возвращает текущий исполняемый процесс
     * 
     * @access public
     * @return object
     */
    public function getProcess() { return $this->c('process')->getProcess(); }
    
    /**
     * Получение текущего URL в режиме Core::HTTP, в режиме Core::CLI 
     * возвращает null
     * 
     * @access public
     * @return null|string
     */
    public function getUrl()
    {
        if ($this->hasHttp())
            return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        else
            return null;
    }
    
    /**
     * Возвращает true, если запрос был через AJAX
     * 
     * @access public
     * @return boolean
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    
    /**
     * Делает переход на указанный процесс, api-метод с параметрами
     * 
     * @access public
     * @param string $process
     * @param null|string $api
     * @param string|array $params
     * @return void
     */
    public function redirect($process, $api = null, $params = array())
    {
        $url = 'index.php?e=' . $process . ($api ? '&f=' . $api : '');
        if (is_array($params))
        {
            foreach($params as $name => $value)
                $url .= '&' . $name . '=' . urlencode($value);
        }
        else
        if (is_string($params) && !empty($params))
            $url .= '&' . $params;
        $this->redirectUrl($url);
    }
    
    /**
     * Переход на указанный URL
     * 
     * @access public
     * @param string $url
     * @return void
     */
    public function redirectUrl($url)
    {
        header('Location: ' . $url);
        exit(0);
    }
}

/** 
 * Класс исключений приложения
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class ApplicationException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
