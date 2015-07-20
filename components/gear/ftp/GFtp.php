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
    protected $_properties =
    [
        'uri' => null,
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'pasv' => false,
        'port' => 21,
        'timeout' => 90,
    ];
    protected $_handler = null;
    /* Public */

    /**
     * Подключение к ftp-серверу
     *
     * @access public
     * @param null|string $uri
     * @return $this
     */
    public function connect($uri = null)
    {
        if ($this->event('onBeforeConnect'))
        {
            if ($this->isConnected())
                $this->close();
            $uri = $uri ?: $this->uri;
            if ($uri)
            {

//                preg_match("/(ftp:\/\/)?((.*?):(.*?)@)?(.*?)(\/.*)/i", $uri, $match);
                preg_match('/(?:(?:ht|f)tps?:\/\/)?(?:[\\-\\w]+:[\\-\\w]+@)?(?:[0-9a-z][\\-0-9a-z]*[0-9a-z]\\.)+[a-z]{2,6}(?::\\d{1,5})?(?:[?\/\\\\#][?!^$.(){}:|=[\\]+\\-\/\\\\*;&~#@,%\\wА-Яа-я]*)?/i', $uri, $match);
                //preg_match("/(ftp:\/\/)?(([-a-z0-9_&?=+,.!~*'%$]):([-a-z0-9_&?=+,.!~*'%$])@)?(.*?)(\/.*)/i", $uri, $match);
                //preg_match("#(http://)?([-a-z0-9_.]+[-a-z0-9_:@&?=+,.!/~*'%$])*#i", $uri, $match);
                echo "uri = $uri\n";
                print_r($match);
                return $this;
            }
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
