<?php

namespace Gear\Library;

use Gear\Interfaces\GEventInterface;
use Gear\Interfaces\GObjectInterface;
use Gear\Traits\TGetter;
use Gear\Traits\TProperties;
use Gear\Traits\TSetter;

/**
 * Базовый класс событий
 *
 * @package Gear Framework
 *
 * @property bool bubble
 * @property iterable properties
 * @property object|null sender
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GEvent implements GEventInterface
{
    /* Traits */
    use TProperties;
    use TGetter;
    use TSetter;
    /* Const */
    /* Private */
    /* Protected */
    protected $_bubble = true;
    protected $_sender = null;
    protected $_properties = [];
    /* Public */

    /**
     * Конструктор события
     *
     * @param string|GObjectInterface $sender
     * @param array $params
     * @since 0.0.1
     * @version 0.0.2
     */
    public function __construct($sender, array $params = [])
    {
        $this->sender = $sender;
        foreach ($params as $name => $value) {
            $this->$name = $value;
        }
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
     * Возвращает поставщика события
     *
     * @return null|GObjectInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getSender(): ?GObjectInterface
    {
        return $this->_sender;
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
     * Установка поставщика события
     *
     * @param string|GObjectInterface $sender
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setSender($sender)
    {
        $this->_sender = $sender;
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
