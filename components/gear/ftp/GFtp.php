<?php

namespace gear\components\gear\ftp;
use gear\Core;
use gear\library\GComponent;

/**
 * Компонент для работы с ftp
 *
 * @package Gear Framework
 * @component Loader
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 20.07.2015
 * @php 5.4.x
 */
class GFtp extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_defaults =
    [
        'uri' => '',
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'pasv' => false,
        'port' => 21,
        'timeout' => 90,
        'remoteDir' => '',
    ];
    protected $_properties = array();
    protected $_handler = null;
    /* Public */

    public static function __callStatic($name, $settings = array())
    {
        return (new self())->connect($name, count($settings) ? $settings[0] : array());
    }

    /**
     * Подключение к ftp-серверу
     *
     * @access public
     * @param null|string $uri
     * @param array $props
     * @return $this
     */
    public function connect($uri = null, array $settings = array())
    {
        if ($this->event('onBeforeConnect'))
        {
            if ($this->isConnected())
                $this->close();
            $uri = $uri ?: $this->uri;
            if ($uri)
            {
                $this->props($this->_defaults);
                echo "uri = $uri\n";
                $uri = str_replace('ftp://', '', $uri);
                if (preg_match("/^(.*?):(.*?)@/i", $uri, $match))
                {
                    $this->username = $match[1];
                    $this->password = $match[2];
                    $uri = preg_replace('#^' . preg_quote($match[0]) . '#', '', $uri);
                }
                if (preg_match("#(.*?):(\d+)#i", $uri, $match))
                {
                    $this->host = $match[1];
                    $this->port = $match[2];
                    $uri = str_replace($this->host . ':' . $this->port, '', $uri);
                    $this->remoteDir = $uri ?: '';
                }
                else
                {
                    if (preg_match("#(.*?)(\/.*)#i", $uri, $match))
                    {
                        $this->host = $match[1];
                        $this->remoteDir = $match[2];
                    }
                    else
                        $this->host = $uri;
                }
            }
            $this->props($settings);
            if (!(@ftp_connect($this->host, $this->port, $this->timeout)))
                $this->e('Error connected to host ' . $this->host);
            $this->login();
            $this->event('onAfterConnect');
        }
        return $this;
    }

    /**
     * Закрывает соединение с сервером
     *
     * @access public
     * @return $this
     */
    public function close()
    {
        if ($this->event('onBeforeClose'))
        {
            if ($this->isConnected())
                ftp_close($this->_handler);
            $this->event('onAfterClose');
        }
        return $this;
    }

    /**
     * Возвращает true, если соединение с сервером установлено
     *
     * @access public
     * @return bool
     */
    public function isConnected() { return is_resource($this->_handler); }

    /**
     * Авторизация на ftp-сервере
     *
     * @access public
     * @param null|string $username
     * @param null|string $password
     * @return $this
     */
    public function login($username = null, $password = null)
    {
        if ($username)
            $this->username = $username;
        if ($password)
            $this->password = $password;
        if ($this->event('onBeforeLogin', $username, $password))
        {
            if (!@ftp_login($this->_handler, $this->username, $this->password))
                $this->e('Invalid login');
            @ftp_pasv($this->_handler, $this->pasv);
            $this->event('onAfterLogin');
        }
        return $this;
    }
}
