/**
 * Класс приложения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GApplication extends GObject implements ApplicationInterface {
    /* Private */
    /* Protected */
    protected _controllers: ControllersCollectionInterface;
    protected _bootstrap: BootstrapFunction;
    protected _request: RequestClass;
    /* Public */

    /**
     * Возвращает функцию, которая отвечает за первичную инициализацию приложения
     *
     * @returns {BootstrapFunction}
     * @since 0.0.1
     * @version 0.0.1
     */
    get bootstrap(): BootstrapFunction {
        return this._bootstrap;
    }

    get controllers(): ControllersCollectionInterface {
        return this._controllers;
    }

    /**
     * Возвращает высоту viewport'а браузера
     *
     * @returns {number}
     * @since 0.0.1
     * @version 0.0.1
     */
    get height(): number {
        return parseInt($(window).attr('innerHeight'));
    }

    /**
     * Возвращает последний совершенный запрос
     *
     * @returns {RequestClass | undefined}
     * @since 0.0.1
     * @version 0.0.1
     */
    get lastRequest(): RequestClass|undefined {
        return this.request;
    }

    /**
     * Возвращает объект запроса к серверу
     *
     * @returns {RequestClass}
     * @since 0.0.1
     * @version 0.0.1
     */
    get request(): RequestClass {
        this._request = new RequestClass({app: this});
        return this._request;
    }

    /**
     * Возвращает ширину viewport'а браузера
     *
     * @returns {number}
     * @since 0.0.1
     * @version 0.0.1
     */
    get width(): number {
        return $(window).width();
    }

    /**
     * Установка функции, которая отвечает за первичную инициализацию приложения
     *
     * @param {BootstrapFunction} bs
     * @since 0.0.1
     * @version 0.0.1
     */
    set bootstrap(bs: BootstrapFunction) {
        this._bootstrap = bs;
    }

    set controllers(controllers: ControllersCollectionInterface) {
        this._controllers = controllers;
    }

    /**
     * Устанавлиает текущий запрос к серверу
     *
     * @param {RequestClass} request
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    set request(request: RequestClass) {
        this._request = request;
    }

    /**
     * Изменение контента внтури объекта, согласно биндингам.
     * Вызывается после успешного запроса к серверу. Должен вызываться из подписанного объектом обработчика события
     * AppClass.onChangeContent
     *
     * @param object data
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public changeContent(data: any, target?: ObjectClass): void {
        let bindName: string;
        let bind: any;
        let dataBindElement: any;
        if (target === undefined) {
            target = this;
        }
        if (target.jq !== undefined) {
            this.beforeChangeContent(target);
            for(bindName in data) {
                bind = data[bindName];
                if (target.jq.attr('data-bind') === bindName) {
                    dataBindElement = target.jq;
                } else {
                    dataBindElement = target.jq.find(`[data-bind="${bindName}"]`);
                    if (dataBindElement.length == 0) {
                        return;
                    }
                }
                target.beforeChangeBind(bindName, bind, dataBindElement);
                if (bind.options !== undefined) {
                    if (bind.options.append) {
                        dataBindElement.append(bind.content);
                    } else if (bind.options.prepend) {
                        dataBindElement.prepend(bind.content);
                    }
                } else {
                    dataBindElement.html(bind);
                }
                target.afterChangeBind(bindName, bind, dataBindElement);
            }
            this.afterChangeContent(target);
            $(window).trigger('resize');
        }
    }

    /**
     * Инициализация приложения
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public onAfterCostruct(): boolean {
        let app: GApplication = this;
        $(window).on('resize', (event: Event) => {
            this.resize(event);
        });
        this.on('requestError', (sender: any, xhr?: JQueryXHR, params: AnyObjectInterface = {}) => {
            this.requestError(xhr);
        });
        this.on('requestSuccess', (sender: any, xhr?: JQueryXHR, params: AnyObjectInterface = {}) => {
            this.requestSuccess(params);
        });
        this.on('responseError', (sender: any, xhr?: JQueryXHR, params: AnyObjectInterface = {}) => {
            this.responseError(params.data.status);
        });
        this.on('beforeChangeContent', (sender: any, xhr?: JQueryXHR, params: AnyObjectInterface = {}) => {
            params.target.trigger('beforeChangeContent');
        });
        this.on('afterChangeContent', (sender: any, xhr?: JQueryXHR, params: AnyObjectInterface = {}) => {
            params.target.trigger('afterChangeContent');
        });
        return true;
    }

    /**
     * Инициализация значений по-умолчанию свойств объекта
     *
     * @return {ObjectClass}
     * @since 0.0.2
     * @version 0.0.2
     */
    public initDefaultProperties(): ObjectInterface {
        this.properties = {
            controllers: {
                auth: 'user/auth',
                denied: 'user/denied'
            },
            requestErrorHandlers: {
                401: function() {
                    window.location.href = `?r=${App.props('controllers').auth}`;
                },
                403: function() {
                    window.location.href = `?r=${App.props('controllers').denied}`;
                }
            },
            onInit: [],
            onConstruct: [],
        };
        return this;
    }

    /**
     * Обработчик ошибок после запроса (HTTP вернул не 200 OK)
     *
     * @param {JQueryXHR} xhr
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public requestError(xhr: JQueryXHR): void {
        if (this.properties.requestErrorHandlers[xhr.status] !== undefined) {
            this.properties.requestErrorHandlers[xhr.status]();
        }
    }

    /**
     * Обработчик удачно завершенного запроса
     *
     * @param {AnyObjectInterface} params
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public requestSuccess(params: AnyObjectInterface): void {
        this.changeContent(undefined, params.binds !== undefined ? params.binds : {});
    }

    /**
     * Обработчик ошибок после запроса (Приложение на сервере вернуло не 200 OK)
     *
     * @param {number} status
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public responseError(status: number): void {
        if (this.properties.requestErrorHandlers[status] !== undefined) {
            this.properties.requestErrorHandlers[status]();
        }
    }

    /**
     * Обработчик события, вознкиающего при изменении окна браузера
     *
     * @param {Event} event
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public resize(event: Event): void {
        var padding: number = parseInt($('.content-container').css('padding-top')) + parseInt($('.content-container').css('padding-bottom'));
        $('.content-container').height(this.height - $('.content-container').offset().top - padding);
        this.trigger('resize', event, {});
    }

    /**
     * Запуск приложения
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public run(): void {
        this.bootstrap();
    }
}

declare let App: ApplicationInterface;
