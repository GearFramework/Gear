<?php

namespace gear\library;

use gear\interfaces\IBehavior;
use gear\interfaces\IObject;
use gear\traits\TGetter;
use gear\traits\TSetter;

/**
 * Общий класс поведений
 *
 * @property IObject owner
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GBehavior implements IBehavior
{
    /* Traits */
    use TSetter;
    use TGetter;
    /* Const */
    /* Private */
    /* Protected */
    protected $_owner = null;
    /* Public */

    /**
     * Конструктор поведения
     *
     * @param array $properties
     * @param IObject $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __construct(array $properties, IObject $owner)
    {
        $this->owner = $owner;
        foreach($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Клонирование объекта-поведение
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __clone() {}

    /**
     * Исполнение вызванного поведения

     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke()
    {
        return $this->execute(...func_get_args());
    }

    /**
     * Генерация события onAfterInstallBehavior после установки поведения
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterInstallBehavior()
    {
        return $this->owner->trigger('onAfterInstallBehavior', new GEvent($this, ['target' => $this->owner, 'sender' => $this]));
    }

    /**
     * Генерация события onAfterUninstall при деинсталляции поведения
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterUninstall()
    {
        return $this->owner->trigger('onAfterUninstallBehavior', new GEvent($this, ['target' => $this->owner, 'sender' => $this]));
    }

    /**
     * Генерация события onBeforeInstallBehavior перед инсталляцией поведения
     *
     * @param array $properties
     * @param IObject $owner
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function beforeInstallBehavior(array $properties, IObject $owner)
    {
        return $owner->trigger('onBeforeInstallBehavior', new GEvent(static::class, ['target' => $owner, 'sender' => static::class]));
    }

    /**
     * Исполнение вызванного поведения
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function execute();

    /**
     * Получение владельца поведения
     *
     * @return IObject $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getOwner(): IObject
    {
        return $this->_owner;
    }

    /**
     * Установка поведения
     *
     * @param array $properties
     * @param IObject $owner
     * @return IBehavior
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function install($properties = [], IObject $owner): IBehavior
    {
        static::beforeInstallBehavior($properties, $owner);
        $behavior = static::it($properties, $owner);
        $behavior->afterInstallBehavior();
        return $behavior;
    }

    /**
     * Создание экземпляра плагина
     *
     * @param array $properties
     * @param IObject $owner
     * @return IBehavior
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function it(array $properties = [], IObject $owner): IBehavior
    {
        return new static($properties, $owner);
    }

    /**
     * Установка владельца поведения
     *
     * @param IObject $owner
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setOwner(IObject $owner)
    {
        $this->_owner = $owner;
    }

    /**
     * Удаление поведения из объекта
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstall()
    {
        return $this->afterUninstall();
    }
}