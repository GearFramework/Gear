<?php

namespace Gear\Traits;

use Gear\Interfaces\ObjectInterface;
use Gear\Interfaces\ServiceInterface;

/**
 * Трейт для объектов, которые поддерживают сервисы (компоненты и плагины)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait ServiceContainedTrait
{
    /**
     * Возвращает экземпляр сервиса по его названию
     *
     * @param string $name
     * @param ObjectInterface|null $owner
     * @return ServiceInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function service(string $name, ObjectInterface $owner = null): ServiceInterface
    {
        if (method_exists($this, 'c')) {
            if ($this->isComponent($name)) {
                return $this->c($name, $owner);
            }
        }
        if (method_exists($this, 'p')) {
            if ($this->isPlugin($name)) {
                return $this->p($name, $owner);
            }
        }
        throw static::ServiceException('Object not service <{name}> contained', ['name' => $name]);
    }
}
