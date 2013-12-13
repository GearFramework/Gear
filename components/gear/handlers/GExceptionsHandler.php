<?php

namespace gear\components\gear\handlers;
use \gear\Core;
use \gear\library\GComponent;
use \gear\library\GException;

/**
 * Класс обработчик неперехваченных исключений
 * 
 * @package Gear Framework
 * @component ExceptionsHandler
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 02.08.2013
 */
class GExceptionsHandler extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'handler' => 'exception',
    );
    protected static $_init = false;
    /* Public */
    public $viewPath = array
    (
        'mode' => array
        (
            Core::HTTP => '\\gear\\views\\exceptionHttp', 
            Core::CLI => '\\gear\\views\\exceptionConsole',
        ),
    );
    
    /**
     * Возвращает путь к шаблону отображения
     * 
     * @access public 
     * @return string
     */
    public function getViewPath()
    {
        return $this->viewPath['mode'][Core::getMode()];
    }
    
    /**
     * Обработчик исключений, которые не были перехвачены try {} catch {}
     * 
     * @access public
     * @param Exception $e
     * @return void
     */
    public function exception(\Exception $e)
    {
        try
        {
            if (Core::isHttp())
                ob_end_clean();
            $this->view->render
            (
                $this->getViewPath(), 
                array
                (
                    'exception' => $e instanceof GException ? $e : new GException($e->getMessage())
                )
            );
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
            $this->e('Не указан параметр "handler"');
        set_exception_handler(array($this, $handlerName));
        return true;
    }
}
