<?php

namespace Gear\Library;

use Gear\Interfaces\EventInterface;
use Gear\Interfaces\ObjectInterface;
use Gear\Traits\PropertiesTrait;
use Gear\Traits\GetterTrait;
use Gear\Traits\SetterTrait;

/**
 * Базовый класс событий
 *
 * @package Gear Framework
 *
 * @property bool bubble
 * @property iterable properties
 * @property object|null|string sender
 * @property object|null target
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GEvent implements EventInterface
{
    /* Traits */
    use PropertiesTrait;
    use GetterTrait;
    use SetterTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected bool $_bubble = true;
    protected $_sender = null;
    protected array $_properties = [];
    protected ?ObjectInterface $_target = null;
    /* Public */

    /**
     * Конструктор события
     *
     * @param string|ObjectInterface $sender
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
     * @return null|ObjectInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getSender(): ?ObjectInterface
    {
        return $this->_sender;
    }

    public function getTarget(): ?ObjectInterface
    {
        return $this->_target;
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
     * @param string|ObjectInterface $sender
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setSender($sender)
    {
        $this->_sender = $sender;
    }

    public function setTarget(ObjectInterface $target)
    {
        return $this->_target = $target;
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
