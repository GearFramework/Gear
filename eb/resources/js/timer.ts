/**
 * Таймер
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @since 0.0.1
 * @version 0.0.1
 */
class TimerClass extends ObjectClass implements TimerInterface {
    /* Private */
    /* Protected */
    protected _interval: number = null;
    protected _stopped: boolean = true;
    protected _paused: boolean = false;
    /* Public */
    public properties: any = {
        timer: 0,
        autoStart: false,
        cycle: false,
        handler: (timer: TimerClass): void => {},
        onEnd: [],
        onStart: []
    };

    get interval(): number {
        if (this._interval === null) {
            this._interval = this._prepareInterval(this.properties.timer);
        }
        return this._interval;
    }

    get paused(): boolean {
        return this._paused;
    }

    get stopped(): boolean {
        return this._stopped;
    }

    set interval(interval: number) {
        this._interval = interval;
    }

    set paused(paused: boolean) {
        this._paused = paused;
    }

    set stopped(stopped: boolean) {
        this._stopped = stopped;
    }

    /**
     * Продолжение работы таймера после паузы
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public continue(): void {
        if (this.isPaused()) {
            this.paused = false;
        }
    }

    /**
     * Инициализация таймера
     *
     * @param properties
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public init<T>(properties: T): void {
        if (this.properties.autoStart === true) {
            this.start();
        }
        super.init(properties);
    }

    /**
     * Возвращает true, если таймер стоит на паузе
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    public isPaused(): boolean {
        return this.paused === true;
    }

    /**
     * Возвращает true, если таймер остановлен
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    public isStopped(): boolean {
        return this._stopped === true;
    }

    /**
     * Получение значение интервала времени для тайминга
     *
     * @private
     * @return {number}
     * @since 0.0.1
     * @version 0.0.1
     */
    protected _prepareInterval(interval: number|string): number {
        let timerInterval: number = 0;
        if (typeof interval === "string") {
            let c: any[] = interval.split("");
            let hours: number = 0;
            let minutes: number = 0;
            let seconds: number = 0;
            let v: string = '';
            for(let k in c) {
                if (c[k] === 's') {
                    seconds = parseInt(v);
                    v = '';
                } else if (c[k] === 'm') {
                    minutes = parseInt(v) * 60;
                    v = '';
                } else if (c[k] === 'h') {
                    hours = parseInt(v) * 3600;
                    v = '';
                } else
                    v += c[k];
            }
            timerInterval = (hours + minutes + seconds) * 1000;
        } else if (typeof interval === "number") {
            timerInterval = interval;
        } else {
            timerInterval = 0;
        }
        return timerInterval;
    }

    /**
     * Остановка таймера на паузу
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public pause(): void {
        if (!this.isStopped()) {
            this.paused = true;
        }
    }

    /**
     * Запуск таймера, либо возобновление работы, если таймер стоял на паузе
     *
     * @param TimerInterval interval
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public start(interval?: TimerInterval): void {
        if (interval !== undefined) {
            this.interval = this._prepareInterval(interval);
        }
        if (!this.isPaused() && this.isStopped()) {
            let handlerFunction = (): void => {
                if (this.paused) {
                    setTimeout(handlerFunction, this.interval);
                } else {
                    if (!this.stopped) {
                        this.properties.handler(this);
                        if (this.properties.cycle === true) {
                            setTimeout(handlerFunction, this.interval);
                        } else {
                            this.stop();
                        }
                    }
                }
            };
            this.stopped = false;
            this.paused = false;
            this.trigger('start', null, {timer: this});
            setTimeout(handlerFunction, this.interval);
        } else if (this.isPaused()) {
            this.continue();
        }
    }

    /**
     * Остановка таймера
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public stop(): void {
        this.stopped = true;
        this.trigger('stop', null, {timer: this})
    }
}

$(document).ready(function () {
    App.timer = (properties: Object = {}, jq?: JQuery): TimerInterface => new TimerClass(properties, jq);
});
