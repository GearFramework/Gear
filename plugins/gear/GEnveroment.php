<?php

namespace gear\plugins\gear;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GException;

/**
 * Плагин для работы данных о среде окружения приложения.
 *  
 * @package Gear Framework
 * @plugin Enveroment
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GEnveroment extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'dependency' => '\\gear\\library\\GApplication',
    );
    protected static $_init = false;
    /* Public */
    
    /**
     * Получение значений из глобального массива $_SERVER по названию ключа,
     * либо значение конфигурационного параметра PHP.
     * Название ключа регистронезависимое. Название конфигурационного
     * параметра должно соответствовать оригинальному названию.
     * 
     * Core::app()->env->request_uri;
     * Core::app()->env->DOCUMENT_ROOT;
     * Core::app()->env->httpUserAgent;
     * Core::app()->env->display_errors;
     * 
     * @access public
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        $key = strtoupper(preg_replace('#([a-z])([A-Z])#', '$1_$2', $name));
        if (isset($_SERVER[$key]))
            return $_SERVER[$key];
        else
            return ini_get($name);
    }
    
    /**
     * Установка конфигурационного параметра PHP.
     * 
     * @access public
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        ini_set($name, $value);
    }
    
    /**
     * Получение значений из глобального массива $_SERVER по названию ключа,
     * либо получение или установка значения конфигурационного параметра PHP.
     * Название ключа регистронезависимое. Название конфигурационного
     * параметра должно соответствовать оригинальному названию. Без параметров
     * метод возвращает массив слитый из массива $_SERVER и массива всех 
     * конфигурационных параметров.
     * 
     * Core::app()->env->props();
     * Core::app()->env->props('request_uri');
     * Core::app()->env->props('DOCUMENT_ROOT');
     * Core::app()->env->props('httpUserAgent');
     * Core::app()->env->props('display_errors');
     * Core::app()->env->props('display_errors', false);
     * 
     * @access public
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function props($name = null, $value = null)
    {
        if (!func_num_args())
        {
            return array_merge($_SERVER, ini_get_all(null, false));
        }
        else
        if (func_num_args() == 1)
        {
            $key = strtoupper(preg_replace('#([a-z])([A-Z])#', '$1_$2', $name));
            if (isset($_SERVER[$key]))
                return $_SERVER[$key];
            else
                return ini_get($name);
        }
        else
        {
            ini_set($name, $value);
            return $this;
        }        
    }
    
    /**
     * Проверяет загружено ли указанное PHP-расширение. При запуске метода
     * без параметров возвращает массив всех загруженных расширений
     * 
     * @access public
     * @param string $extensionName
     * @return mixed
     */
    public function loaded($extensionName = null)
    {
        return $extensionName ? extension_loaded($extensionName) : get_loaded_extensions();
    }
    
    /**
     * Get PHP script owner's GID
     * 
     * @access public
     * @return integer 
     */
    public function gid()
    {
        return getmygid();
    }
    
    /**
     * Gets PHP's process ID
     * 
     * @access public
     * @return integer 
     */
    public function pid()
    {
        return getmypid();
    }
    
    /**
     * Gets PHP script owner's UID
     * 
     * @access public
     * @return integer 
     */
    public function uid()
    {
        return getmyuid();
    }
}

/**
 * Обработчик исключений класса среды окружения
 *
 * @package Gear Framework
 * @plugin Enveroment
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class EnveromentException extends GException 
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
