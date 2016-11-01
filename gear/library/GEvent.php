<?php

namespace gear\library;

use gear\interfaces\IObject;
use gear\traits\TGetter;
use gear\traits\TObject;
use gear\traits\TSetter;

/**
 * Базовый класс событий
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GEvent
{
    /* Traits */
    use TObject;
    use TGetter;
    use TSetter;
    /* Const */
    /* Private */
    /* Protected */
    protected $_bubble = true;
    /* Public */

    /**
     * Конструктор события
     * 
     * @param string|IObject $sender
     * @param array $params
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __construct($sender, array $params = [])
    {
        $this->sender = $sender;
        foreach($params as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Установка или отмена всплытия события
     *
     * @param bool $value
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setBubble(bool $value)
    {
        $this->_bubble = $value;
    }

    /**
     * Получение состояния всплытия события
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getBubble(): bool
    {
        return $this->_bubble;
    }

    /**
     * Останавливает всплытие события
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function stopPropagation()
    {
        $this->bubble = false;
    }
}
