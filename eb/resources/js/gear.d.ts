/**
 * Тип интервала таймера
 */
type TimerInterval = number | string;

/**
 * Интерфейс для комонентов-таймеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
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
     * @param TimerInterval interval
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

interface TimerGenerator {
//    (properties: any = {}, jq?: JQuery): TimerInterface;
}