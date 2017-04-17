/**
 * Класс для работы с ajax-запросами
 * - Посылка запросов
 * - Обработка ответов
 *
 * @since 0.0.1
 * @version 0.0.1
 */
class RequestClass extends ObjectClass {
    /* Private */
    /* Protected */
    protected _propertiesDefault: any = {
        messenger: console,
        progress: null,
        dataType:'json',
        method:"GET",
        onInit: [],
        onResponseSuccess: [
            (eventName: string, event?: any, params?: any): void => {
                App.trigger('responseSuccess', event, params);
            }
        ],
        onResponseError: [
            (eventName: string, event?: any, params?: any): void => {
                App.trigger('responseError', event, params);
            }
        ],
        onRequestComplete: [
            (eventName: string, event?: any, params?: any): void => {
                App.trigger('requestComplete', event, params);
            }
        ],
        onRequestError: [
            (eventName: string, event?: any, params?: any): void => {
                App.trigger('requestError', event, params);
            }
        ]
    };
    /* Public */

    /**
     * Конструктор объекта
     *
     * @param Object properties
     * @param JQuery jq
     * @return ObjectClass
     * @since 0.0.1
     * @version 0.0.1
     */
    constructor (properties: Object = {}, jq?: JQuery) {
        super(properties, jq);
        this.props(this._mergeProperties(this._propertiesDefault, properties));
        this.init(properties);
    }

    /**
     * Обработчик события возникающего перед отправкой запроса на сервер
     *
     * @param JQueryXHR xhr
     * @param Object settings
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public beforeSend(xhr: JQueryXHR, settings: Object): void {
        let progress: any = this.props('progress');
        if (progress !== null) {
            progress.start();
        }
        this.trigger('beforeSend', xhr, {setting: settings});
    }

    /**
     * Обработчик события, возникающего когда запрос завершает свою работу не зависимо от была ли ошибка от
     * сервера или сервер вернул 200 OK
     *
     * Генерирует событие App.onRequestComplete
     *
     * @param JQueryXHR xhr
     * @param string textStatus
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public complete(xhr: JQueryXHR, textStatus: string): void {
        let progress: any = this.props('progress');
        if (typeof progress === "object") {
            progress.stop().reset();
        }
        this.trigger('requestComplete', xhr, {textStatus: textStatus});
    }

    /**
     * Обработчик события, возникающего когда сервер возвращает не 200 OK
     *
     * Генерирует событие App.onRequestError
     *
     * @param JQueryXHR xhr
     * @param any status
     * @param string errorMessage
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public error(xhr: JQueryXHR, status: any, errorMessage: string): void {
        let messenger: any = this.props('messenger');
        if (typeof messenger === "object") {
            messenger.log(`Request error [${xhr.status}] ${xhr.statusText}`);
        }
        this.trigger('requestError', xhr, {status: status, errorMessage: errorMessage});
    }

    /**
     * Отправка GET-запроса
     *
     * @param object requestOptions
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public get(requestOptions: any = {method: "GET"}): void {
        console.log('Request get');
        if (requestOptions["method"] === undefined) {
            requestOptions["method"] = "GET";
        }
        console.log(this.properties);
        this.send(requestOptions);
    }

    /**
     * Инициализация объекта
     *
     * @param object properties
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public init(properties: Object = {}): void {
        console.log('Request init');
        super.init(properties);
        console.log(this);
    }

    /**
     * Отправка POST-запроса
     *
     * @param object requestOptions
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public post(requestOptions: any = {method: "POST"}): void {
        if (requestOptions["method"] === undefined) {
            requestOptions["method"] = "POST";
        }
        this.send(requestOptions);
    }

    /**
     * Метод вызывается когда со стороны сервера пришёл ответ содержаший ошибку выполнения
     *
     * Генерирует событие App.onResponseError
     *
     * @param Object data
     * @param JQueryXHR xhr
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public responseError(data: Object, xhr: JQueryXHR): void {
        this.trigger('responseError', xhr, {data: data});
    }

    /**
     * Метод вызывается когда со стороны сервера пришёл ответ, не содержащий в себе
     * ошибку исполнения кода
     *
     * Генерирует событие App.onResponseSuccess
     *
     * @param Object data
     * @param JQueryXHR xhr
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public responseSuccess(data: Object, xhr: JQueryXHR): void {
        this.trigger('responseSuccess', xhr, {data: data});
    }

    /**
     * Отправка запроса на сервер
     *
     * @param object requestOptions
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public send(requestOptions: any): void {
        console.log('Request send');
        console.log(this);
        let request: RequestClass = this;
        let options: any = this.props('requestOptions');
        console.log(options);
        for(let name in requestOptions) {
            options[name] = requestOptions[name];
        }
        options.beforeSend = (xhr: JQueryXHR, settings: Object): void => request.beforeSend(xhr, settings);
        options.success = (data: any, textStatus: string, xhr: JQueryXHR): void => request.success(data, textStatus, xhr);
        options.error = (xhr: JQueryXHR, status: any, errorMessage: string): void => request.error(xhr, status, errorMessage);
        options.complete = (xhr: JQueryXHR, textStatus: string): void => request.complete(xhr, textStatus);
        $.ajax(options);
    }

    /**
     * Обработчик успешно выполненного запроса, когда сервер возвращает ответ 200 OK
     *
     * @param object data
     * @param string textStatus
     * @param JQueryXHR xhr
     * @return void
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public success(data: any, textStatus: string, xhr: JQueryXHR): void {
        let request: RequestClass = this;
        this.trigger('beforeSuccess', xhr, {request: request, data: data, textStatus: textStatus});
        if (!this.props('returnTransfer')) {
            if (data["error"] !== null) {
                let messenger: any = this.props('messenger');
                if (messenger !== null) {
                    messenger.log(`Application error:\n${data.error.text}\n${data.error.file} [${data.error.line}]\n${data.error.trace}`);
                }
                this.responseError(data, xhr);
            } else {
                this.responseSuccess(data, xhr);
            }
            this.trigger('afterSuccess', xhr, {request: request, data: data, textStatus: textStatus});
        } else {
            this.trigger('afterSuccess', xhr, {request: request, data: data, textStatus: textStatus});
        }
    }
}

$(document).ready(function () {
    AppClass.prototype.request = (properties: any, jq?: any): RequestClass => new RequestClass(properties, jq);
});
