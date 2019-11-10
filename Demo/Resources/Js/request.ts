/**
 * Класс для работы с ajax-запросами
 * - Посылка запросов
 * - Обработка ответов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class RequestClass extends ObjectClass {
    /* Private */
    /* Protected */
    protected _ajaxFields: Array<string> = [
        'accepts', 'async',
        'cache', 'contents', 'contentType', 'context', 'converters', 'crossDomain',
        'data', 'dataType',
        'global',
        'headers',
        'ifModified', 'isLocal',
        'jsonp', 'jsonpCallback',
        'mimeType',
        'password', 'processData',
        'scriptCharset', 'statusCode',
        'timeout', 'traditional',
        'url', 'username',
        'xhr', 'xhrFields'
    ];
    /* Public */

    get ajaxFields(): Array<string> {
        return this._ajaxFields;
    }

    get app(): ApplicationClass {
        return this.properties.app;
    }

    /**
     * Обработчик события возникающего перед отправкой запроса на сервер
     *
     * @param {JQueryXHR} xhr
     * @param settings
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public beforeSend(xhr: JQueryXHR, settings: Object): void {
        let progress: any = this.properties.progress;
        if (progress !== null && typeof progress === "object") {
            progress.start();
        }
        this.trigger('beforeSend', xhr, {setting: settings});
    }

    /**
     * Обработчик события, возникающего когда запрос завершает свою работу не зависимо от была ли ошибка от
     * сервера или сервер вернул 200 OK
     *
     * @param {JQueryXHR} xhr
     * @param {string} textStatus
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public complete(xhr: any, textStatus: string): void {
        let progress: any = this.props('progress');
        if (progress !== null && typeof progress === "object") {
            progress.stop();
        }
        this.trigger('requestComplete', xhr, {textStatus: textStatus});
    }

    /**
     * Обработчик события, возникающего когда сервер возвращает не 200 OK
     *
     * @param {JQueryXHR} xhr
     * @param {any} status
     * @param {string} errorMessage
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public error(xhr: any, status: any, errorMessage: string): void {
        if (this.properties.messenger !== undefined) {
            this.properties.messenger.log(`Request error [${xhr.status}] ${xhr.statusText}`);
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
    public init(): ObjectClass {
        this.properties = {
            dataType: 'json',
            messenger: console,
            progress: undefined,
            requestOptions: {},
            onRequestError: [(req: RequestClass, xhr: any, params: any) => {
                this.app.trigger('requestError', xhr, params);
            }],
            onRequestCompletes: [(req: RequestClass, xhr: any, params: any) => {
                this.app.trigger('requestComplete', xhr, params);
            }]
        };
        return this;
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
    public responseError(data: any, xhr: JQueryXHR): void {
        if (this.properties.messenger !== undefined) {
            if (data.error instanceof Array) {
                for (let idx in data.error) {
                    let error: any = data.error[idx];
                    this.properties.messenger.log(`Application error:\n${error.text}\n${error.file} [${error.line}]\n${error.trace}`);
                }
            } else {
                this.properties.messenger.log(`Application error:\n${data.error.text}\n${data.error.file} [${data.error.line}]\n${data.error.trace}`);
            }
        }
        this.app.trigger('responseError', xhr, {data: data});
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
        let options: any = this.mergeProperties(requestOptions);
        this.properties = options;
        let ajax: any = this.ajaxFields;
        for (let field of ajax) {
            if (this.properties[field] !== undefined) {
                options[field] = this.properties[field];
            }
        }
        options.beforeSend = (xhr: JQueryXHR, settings: Object) =>  {
            this.beforeSend(xhr, settings);
        };
        options.success = (data: any, textStatus: string, xhr: JQueryXHR) => {
            this.success(data, textStatus, xhr);
        };
        options.error = (xhr: any, status: any, errorMessage: string) => {
            this.error(xhr, status, errorMessage);
        };
        options.complete = (xhr: any, textStatus: string) => {
            this.complete(xhr, textStatus);
        };
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
        this.trigger('beforeSuccess', xhr, {request: this, data: data, textStatus: textStatus});
        if (!this.properties.returnTransfer) {
            if (data.status != 200) {
                /* Приложение вернуло ошибку во время исполнения запроса */
                this.responseError(data, xhr);
            } else {
                /* Приложение успешно отработало во время исполнения запроса */
                this.responseSuccess(data, xhr);
            }
            this.trigger('requestSuccess', xhr, data);
        }
    }
}
