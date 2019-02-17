<?php

namespace Gear\Interfaces;

/**
 * Интерфейс контроллеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ControllerInterface
{
    /**
     * Вызов метода $this->exec()
     *
     * @param RequestInterface $request
     * @return mixed
     * @since 0.0.1
     * @version 0.0.2
     */
    public function __invoke(RequestInterface $request);

    /**
     * Запуск контроллера
     *
     * @param RequestInterface $request
     * @return mixed
     * @since 0.0.1
     * @version 0.0.2
     */
    public function run(RequestInterface $request);
}

/**
 * Интерфейс api-методов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ApiInterface
{
    /**
     * Вызов метода $this->exec();
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke();

    /**
     * Вызов api-метода
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exec();
}
