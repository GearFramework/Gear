<?php

namespace Gear\Traits;

use Gear\Core;
use Gear\Interfaces\EventHandlerInterface;
use Gear\Interfaces\EventInterface;


/**
 * Трэйт для включения в класс методов для работы с событиями
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait EventTrait
{
    /**
     * Добавление обработчика указанного события
     *
     * @param string $name
     * @param string|array|callable|\Closure|EventHandlerInterface $handlers
     * @throws \EventException
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function attachEvent(string $name, $handlers)
    {
        if (is_array($handlers) && !is_callable($handlers)) {
            foreach ($handlers as $handler) {
                $this->attachEvent($name, $handler);
            }
        } else {
            if (!isset($this->_events[$name])) {
                $this->_events[$name] = [];
            }
            if (is_array($handlers) && isset($handlers['class'])) {
                list($class,, $properties) = Core::configure($handlers);
                $handlers = new $class($properties, $this);
            }
            if (!is_callable($handlers)) {
                throw new \EventException('Invalid event <{event}> handler ', ['event' => static::class . '::' . $name]);
            }
            $this->_events[$name][] = $handlers;
        }
    }

    /**
     * Получение списка событий и их обработчиков
     *
     * @return array $events
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getEvents(): array
    {
        return $this->_events;
    }

    /**
     * Удаление обработчика/ов указанного события
     *
     * @param string|array $eventName
     * @param null|callable|\Closure|EventHandlerInterface $handler
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function off(string $eventName, $handler = null)
    {
        if ($handler === null && isset($this->_events[$eventName])) {
            unset($this->_events[$eventName]);
        } elseif (isset($this->_events[$eventName])) {
            foreach ($this->_events[$eventName] as $h) {
                if ($h === $handler)
                    unset($this->_events[$eventName]);
            }
        }
    }

    /**
     * Установка обработчика/ов на событие
     *
     * @param string|array $eventName
     * @param null|array|callable|\Closure|EventHandlerInterface $handler
     * @return void
     * @throws \EventException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function on(string $eventName, $handler)
    {
        if (!isset($this->_events[$eventName])) {
            $this->_events[$eventName] = [];
        }
        $this->attachEvent($eventName, $handler);
    }

    /**
     * Установка событий и их обработчиков
     *
     * @param array $events
     * @return void
     * @throws \EventException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setEvents(array $events)
    {
        $this->_events = [];
        foreach($events as $name => $handlers) {
            $this->attachEvent($name, $handlers);
        }
    }

    /**
     * Генерация события
     *
     * @param string $eventName
     * @param EventInterface $event
     * @return bool
     * @since 0.0.1
     * @version 0.0.2
     */
    public function trigger(string $eventName, EventInterface $event)
    {
        $result = true;
        if (method_exists($this, $eventName)) {
            $result = $this->$eventName($event);
            if (!$event->bubble)
                return $result;
        }
        if (isset($this->_events[$eventName])) {
            foreach ($this->_events[$eventName] as $handler) {
                if (call_user_func($handler, $event) === false)
                    $result = false;
                if (!$event->bubble)
                    break;
            }
        }
        return $result;
    }
}
