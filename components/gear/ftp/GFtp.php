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
class GFtp extends GComponent implements \IteratorAggregate
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected static $_defaultProperties =
    [
        'uri' => '',
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'pasv' => false,
        'port' => 21,
        'timeout' => 90,
        'remoteDir' => '/',
    ];
    protected $_properties = [];
    protected $_handler = null;
    /* Public */

    public static function __callStatic($name, $settings = [])
    {
        return (new self())->connect($name, count($settings) ? $settings[0] : []);
    }

    public function getIterator()
    {
        return GFtpFolder(['path' => $this->remoteDir], $this);
    }

    /**
     * Возвращает ресурс соединения
     *
     * @access public
     * @return null|resource
     */
    public function getHandler() { return $this->_handler; }

    /**
     * Подключение к ftp-серверу
     *
     * @access public
     * @param null|string $uri
     * @param array $settings
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
                $this->props(self::$_defaultProperties);
                $uri = str_replace('ftp://', '', $uri);
                if (preg_match("/^(.*?):(.*?)@/i", $uri, $match))
                {
                    list(, $this->username, $this->password) = $match;
                    $uri = preg_replace('#^' . preg_quote($match[0]) . '#', '', $uri);
                }
                if (preg_match("#(.*?):(\d+)#i", $uri, $match))
                {
                    list(, $this->host, $this->port) = $match;
                    $uri = str_replace($this->host . ':' . $this->port, '', $uri);
                    $this->remoteDir = $uri ?: '';
                }
                else
                    preg_match("#(.*?)(\/.*)#i", $uri, $match) ? list(, $this->host, $this->remoteDir) = $match : $this->host = $uri;
            }
            $this->props($settings);
            if (!($this->_handler = @ftp_connect($this->host, $this->port, $this->timeout)))
                $this->e('Error connected to host ' . $this->host . ':' . $this->port);
            $this->login();
            if ($this->remoteDir && $this->remoteDir !== '/')
                $this->chDir();
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
            $this->pasv();
            $this->event('onAfterLogin');
        }
        return $this;
    }

    /**
     * Установка/снятие пассивного режима
     *
     * @access public
     * @param null|boolean $pasv
     * @return $this
     */
    public function pasv($pasv = null)
    {
        @ftp_pasv($this->_handler, $pasv !== null ? (bool)$pasv : (bool)$this->pasv);
        return $this;
    }

    /**
     * Смена удалённой директории
     *
     * @access public
     * @param null|string $dir
     * @return $this
     */
    public function chDir($dir = null)
    {
        $dir = $dir !== null ? $dir : $this->remoteDir;
        if ($dir{0} === '/')
            $dir = '.' . $dir;
        if (!@ftp_chdir($this->_handler, $dir))
            $this->e('Failed to changed directory ' . ($dir !== null ? $dir : $this->remoteDir));
        return $this;
    }
}
