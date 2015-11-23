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
    protected $_tests = array();
    /* Public */
    public $rules = array();

    /**
     * Установка списка тестов
     *
     * @access public
     * @param array $tests
     * @return $this
     */
    public function setTests($tests)
    {
        $this->_tests = $tests;
        return $this;
    }

    /**
     * Возвращает списко тестов
     *
     * @access public
     * @return array
     */
    public function getTests() { return $this->_tests; }
    
    /**
     * Производит конфигурирование и проверку приложения
     * 
     * @access public
     * @return boolean
     */
    public function preloadTest()
    {
        if ($this->buffer)
            ob_start();
        if ($this->session && !ini_get('session.auto_start'))
            session_start();
        if (version_compare(PHP_VERSION, $this->php, '<='))
            $this->e('Invalid php version. Your version is ' . PHP_VERSION . ' needle ' . $this->php);
        foreach($this->extensions as $extension)
            if (!extension_loaded($extension))
                $this->e('PHP extension ":extensionName" not installed', array('extensionName' => $extension));
        return true;
    }

    /**
     * Исполнение пользовательского теста
     *
     * @access public
     * @param string|array|\Closure $callback
     * @return $this
     * @throws mixed
     */
    public function test($callback = null)
    {
        if (!$callback)
            $callback = $this->tests;
        if (is_callable($callback))
        {
            if (!$callback($this))
                $this->e('Error configuration test');
        }
        else
        if (is_array($callback))
        {
            foreach($callback as $test)
                $this->test($test);
        }
        return $this;
    }
    
    /**
     * Обработчик события, возникающего после инсталляции компонента.
     * 
     * @access public
     * @return boolean
     */
    public function onInstalled() { return $this->preloadTest(); }
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
