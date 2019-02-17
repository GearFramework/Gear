<?php

namespace Gear\Library;

use Gear\Interfaces\ApiInterface;

/**
 * Класс api-методов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GApi extends GModel implements ApiInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Запуск api-метода
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke()
    {
        return $this->exec();
    }

    /**
     * Запуск api-метода
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function exec();
}
