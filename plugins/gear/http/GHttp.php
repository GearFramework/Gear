<?php

namespace gear\plugins\gear\http;
use gear\Core;
use gear\library\GPlugin;
use gear\library\GException;

/**
 * Плагин для работы с http
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 21.12.2014
 * @php 5.3.x
 * @release 1.0.0
 */
class GHttp extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected static $_defaultProperties = array
    (
        'flushDataOnDestroy' => true,
    );
    protected $_header = array('class' => 'gear\plugins\gear\http\GHeader');
    protected $_sender = array('class' => 'gear\components\gear\curl\GCurl');
    /* Public */

    /**
     * Деструктор класса, если flushDataOnDestroy в true, то выводятся буферизированные данные
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        if ($this->flushDataOnDestroy && $this->_outputData)
            echo $this->_outputData;
    }

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
     * Получение или установка заголовков, через плагин, работающий с Http-заголовками
     *
     * @access public
     * @param null $headerName
     * @param null $headerValue
     * @return mixed|object
     */
    public function header($headerName = null, $headerValue = null)
    {
        $header = $this->getHeader();
        return !func_num_args() ? $header : call_user_func_array($header, func_get_args());
    }

    /**
     * Получение плагина, работающего с Http-заголовками
     *
     * @access public
     * @return object
     */
    public function getHeader()
    {
        if (!$this->owner->isComponentRegistered('header'))
            $this->owner->registerComponent('header', $this->_header);
        return $this->owner->c('header');
    }

    /**
     * Установка плагина для работы с Http-заголовками
     *
     * @access public
     * @param array|object $header
     * @return $this
     */
    public function setHeader($header)
    {
        if (is_object($header) || is_array($header))
            $this->_header = $header;
        else
            $this->e('Incorrect header plugin');
        return $this;
    }

    public function getSender()
    {
        if (!$this->owner->isComponentRegistered('sender'))
            $this->owner->registerComponent('sender', $this->_sender);
        return $this->owner->c('sender');
    }

    public function setSender($sender)
    {
        if (is_object($sender) || is_array($sender))
            $this->_sender = $sender;
        else
            $this->e('Incorrect sender plugin');
        return $this;
    }

    public function send($url, $method = 'GET', $params = array(), $headers = array(), $callbackResponse = null)
    {
        $method = strtolower($method);
        if (!method_exists($this, $method))
            $this->e('Invalid http request type');
        return $this->$method($url, $params, $headers, $callbackResponse);
    }

    public function get($url, $params = array(), $headers = array(), $callbackResponse = null)
    {
        $result = $this->sender->get($url, $params, $headers);
        return $callbackResponse && is_callable($callbackResponse) ? $callbackResponse($result) : $result;
    }
}

/**
 * Исключения плагина для работы с http
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 21.12.2014
 * @php 5.3.x
 */
class HttpException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
