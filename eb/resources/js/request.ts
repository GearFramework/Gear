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
    /* Public */
    public properties: any = {
        messenger: console,
        progress: null,
        requestOptions: {},
        onInit: []
    };

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
        if (typeof progress === "object") {
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
    public complete(xhr: any, textStatus: string): void {
        let progress: any = this.props('progress');
        if (typeof progress === "object") {
            progress.stop().reset();
        }
        App.trigger('requestComplete', xhr, {textStatus: textStatus});
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
    public error(xhr: any, status: any, errorMessage: string): void {
        let messenger: any = this.props('messenger');
        if (typeof messenger === "object") {
            messenger.log(`Request error [${xhr.status}] ${xhr.statusText}`);
        }
        App.trigger('requestError', xhr, {status: status, errorMessage: errorMessage});
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
        if (requestOptions["method"] === undefined) {
            requestOptions["method"] = "GET";
        }
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
        super.init(properties);
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
        App.trigger('responseError', xhr, {data: data});
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
        App.trigger('responseSuccess', xhr, {data: data});
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
        let options: any = this.props('requestOptions');
        for(let name in requestOptions) {
            options[name] = requestOptions[name];
        }
        options.beforeSend = this.beforeSend;
        options.success = this.success;
        options.error = this.error;
        options.complete = this.complete;
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
                if (typeof messenger === "object") {
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
    AppClass.prototype.request = (properties: any, jq?: any) => new RequestClass(properties, jq);
});
