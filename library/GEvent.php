<?php

namespace gear\library;

/**
 * Класс объектов-событий 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class GEvent
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $sender = null;
    
    /**
     * Конструктор события
     * 
     * @access public
     * @param object $sender
     * @return void
     */
    public function __construct($sender)
    {
        $this->sender = $sender;
    }
}
