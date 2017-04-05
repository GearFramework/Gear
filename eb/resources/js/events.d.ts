/**
 * Интерфейс обработчиков событий
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @since 0.0.1
 * @version 0.0.1
 */
interface EventHandler {
    (eventName: string, event?: any, params?: any): void;
}
