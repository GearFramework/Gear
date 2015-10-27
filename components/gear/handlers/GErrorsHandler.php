<?php

namespace gear\components\gear\handlers;
use \gear\Core;
use \gear\library\GComponent;
use \gear\library\GException;

/**
 * Класс обработчик неперехваченных исключений и ошибок
 * 
 * @package Gear Framework
 * @component ErrorsHandler
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 02.08.2013
 * @php 5.3.x
 */
class GErrorsHandler extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array('handler' => 'error');
    protected static $_init = false;
    protected $_viewPath = array
    (
        'mode' => array
        (
            Core::HTTP => '\gear\views\errorHttp',
            Core::CLI => '\gear\views\errorConsole',
        ),
    );
    /* Public */

    /**
     * Возвращает путь к шаблону отображения
     * 
     * @access public 
     * @return string
     */
    public function getViewPath() { return $this->_viewPath['mode'][Core::getMode()]; }
    
    /**
     * Обработчик ошибок php
     * 
     * @access public
     * @param integer $code
     * @param string $message
     * @param string $file
     * @param integer $line
     * @return void
     */
    public function error($code, $message, $file = '', $line = 0)
    {
        echo "Error handler -> $message\n";
        $args = array('exception' => new GException($message), 'code' => $code, 'file' => $file, 'line' => $line);
        try
        {
            $this->view->render($this->getViewPath(), $args);
        }
        catch(\Exception $e)
        {
            die($e->getMessage());
        }
        exit();
    }
    
    /**
     * Получение куска исходного кода указанного php-файла относительно
     * указанного номера строки
     * 
     * @access public
     * @param string $file
     * @param integer $currentLine
     * @return array
     */
    public function getSource($file, $currentLine = 0)
    {
        $sources = array();
        if (is_file($file) && file_exists($file) && is_readable($file))
        {
            $lines = file($file);
            $count = count($lines);
            if (!$currentLine)
            {
                $startLine = 0;
                $endLine = $count - 1;
            }
            else
            {
                $startLine = $currentLine - 10 >= 0 ? $currentLine - 10 : 0;
                $endLine = $currentLine + 10 < $count ? $currentLine + 9 : $count - 1;
            }
            for($i = $startLine; $i <= $endLine; ++ $i)
            {
                $sources[$i + 1] = $lines[$i];
            }
        }
        return $sources;
    }
    
    /**
     * Подсветка синтаксиса
     * 
     * @access public
     * @param string $source
     * @return string
     */
    public function highlight($source)
    {
        $source = highlight_string($source, true);
        return $source;
    }

    /**
     * Обработчик события onInstalled по-умолчанию
     * 
     * @access public
     * @param GEvent $event
     * @return void
     */
    public function onInstalled($event)
    {
        if (!($handlerName = $this->i('handler')))
            throw $this->exceptionService('Not specified "handler"');
        set_error_handler(array($this, $handlerName), E_ALL);
        return true;
    }
}
