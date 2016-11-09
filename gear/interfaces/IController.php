<?php

namespace gear\interfaces;

/**
 * Интерфейс контроллеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IController
{
    /**
     * Вызов метода $this->exec()
     *
     * @param IRequest|null $request
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke(IRequest $request = null);

    /**
     * Запуск контроллера
     *
     * @param IRequest|null $request
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exec(IRequest $request = null);
}

/**
 * Интерфейс api-методов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IApi
{
    /**
     * Вызов метода $this->exec();
     * 
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke();

    public function exec();
}
