/**
 * Интерфейс приложения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface ApplicationInterface {}

/**
 * Интерфейс bootstrap-функции приложения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface BootstrapFunction {
    (): void;
}

/**
 * Интерфейс динамических свойств объекта
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface ObjectPropertiesInterface {
    [key: string]: any
}

/**
 * Интерфейс для комонентов-таймеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface TimerInterface {
    /**
     * Продолжение работы таймера после паузы
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    continue(): void;

    /**
     * Возвращает true, если таймер стоит на паузе
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    isPaused(): boolean;

    /**
     * Возвращает true, если таймер остановлен
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    isStopped(): boolean;

    /**
     * Остановка таймера на паузу
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    pause(): void;

    /**
     * Запуск таймера, либо возобновление работы, если таймер стоял на паузе
     *
     * @param {TimerInterval|undefined} interval
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    start(interval?: TimerInterval): void;

    /**
     * Остановка таймера
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    stop(): void;
}

interface TimerGeneratorInterface {
    (properties?: Object, jq?: JQuery): TimerInterface;
}

/**
 * Тип интервала таймера
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
type TimerInterval = number | string;
