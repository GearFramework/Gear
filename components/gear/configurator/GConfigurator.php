<?php

namespace gear\components\gear\configurator;
use \gear\Core;
use \gear\library\GComponent;
use \gear\library\GException;

/** 
 * Компонент конфигурирования и проверки зависимостей приложения
 * 
 * @package Gear Framework
 * @component Configurator
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 02.08.2013
 */
class GConfigurator extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_properties = array
    (
        'buffer' => false,
        'session' => false,
        'php' => '5.3.0',
        'extensions' => array()
    );
    /* Public */
    public $rules = array();
    
    /**
     * Производит конфигурирование и проверку приложения
     * 
     * @access public
     * @return void
     */
    public function run()
    {
        if ($this->buffer)
            ob_start();
        if ($this->session && !ini_get('session.auto_start'))
            session_start();
        if (version_compare(PHP_VERSION, $this->php, '<='))
            $this->e('Текущая версия PHP ' . PHP_VERSION . ' не соответствует требуемой ' . $this->php);
        foreach($this->extensions as $extension)
            if (!extension_loaded($extension))
                $this->e('Расширение PHP ":extensionName" не установлено', array('extensionName' => $extension));
    }
    
    /**
     * Обработчик события, возникающего после инсталляции компонента.
     * 
     * @access public
     * @return boolean
     */
    public function onInstalled()
    {
        $this->run();
        return true;
    }
}

/** 
 * Исключения компонента конфигурирования
 * 
 * @package Gear Framework
 * @component Configurator
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 02.08.2013
 */
class ConfiguratorException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
