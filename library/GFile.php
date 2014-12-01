<?php

namespace gear\library;
use gear\Core;
use gear\library\GFileSystem;
use gear\library\GException;

/**
 * Обычный файл
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
class GFile extends GFileSystem
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    /**
     * Возвращает размер элемента
     * 
     * @abstract
     * @access public
     * @return integer
     */
    public function size($format = null) 
    { 
        if ($format)
        {
            $filesizename = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
            return $size
            ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i]
            : '0 ' . $filesizename[0];            
        }
        return filesize($this->path); 
    }
}

/**
 * Исключения генерируемый файлом
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 30.11.2014
 */
class FileException  extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
