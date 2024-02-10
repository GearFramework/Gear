<?php

namespace Gear\Plugins\Http;

use ArrayAccess;
use Gear\Interfaces\Http\ServerInterface;
use Gear\Library\Services\Plugin;
use Gear\Traits\Types\ArrayAccessTrait;
use Gear\Traits\Types\IteratorTrait;
use Iterator;

/**
 * Плагин для работы с web-сервером и окружением
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Server extends Plugin implements ServerInterface, ArrayAccess, Iterator
{
    /* Traits */
    use ArrayAccessTrait;
    use IteratorTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected array $items = [];
    /* Public */

    public function __get(string $name): mixed
    {
        $serverKey = strtoupper(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
        if (isset($this[$serverKey])) {
            return $this[$serverKey];
        }
        return parent::__get($name);
    }

    /**
     * Выполняется после установки сервиса
     *
     * @return void
     */
    public function afterInstall(): void
    {
        $this->items = $_SERVER;
    }
}
