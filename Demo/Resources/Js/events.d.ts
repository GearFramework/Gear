interface EventInterface {
    [key: string]: any;
}

/**
 * Интерфейс обработчиков событий
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface EventHandlerInterface {
    (sender: any, event?: any, params?: EventParamsInterface): void;
}

interface EventParamsInterface {
    [key: string]: any;
}
