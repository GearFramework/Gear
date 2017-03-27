/**
 * Класс для работы с ajax-запросами
 * - Посылка запросов
 * - Обработка ответов
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

    public beforeSend(xhr: any, settings: any): void {
        let progress: any = this.props('progress');
        if (typeof progress === "object") {
            progress.start();
        }
        App.trigger('beforeSend', xhr, {setting: settings});
    }

    public complete(xhr: any, textStatus: string): void {
        let progress: any = this.props('progress');
        if (typeof progress === "object") {
            progress.stop().reset();
        }
        App.trigger('complete', xhr, {textStatus: textStatus});
    }

    public error(xhr: any, status: any, errorMessage: string): void {
        let messenger: any = this.props('messenger');
        if (typeof messenger === "object") {
            messenger.log(`Request error [${xhr.status}] ${xhr.statusText}`);
        }
        App.trigger('errorResponse', xhr, {status: status, errorMessage: errorMessage});
    }

    /**
     * Отправка GET-запроса
     *
     * @param object requestOptions
     * @since 2.0.0
     * @version 2.0.0
     */
    public get(requestOptions: any = {}): void {
        typeof requestOptions === "undefined" ? requestOptions = {method: "GET"} : requestOptions["method"] = "GET";
    }

    /**
     * Отправка POST-запроса
     *
     * @param object requestOptions
     * @since 2.0.0
     * @version 2.0.0
     */
    public post(requestOptions: any = {}): void {
        typeof requestOptions === "undefined" ? requestOptions = {method: "POST"} : requestOptions["method"] = "POST";
    }

    public responseError(data: any, xhr: any): void {
        this.trigger('responseError', undefined, {data: data, xhr: xhr});
    }

    public responseSuccess(data: any, xhr: any): void {
        this.trigger('responseSuccess', undefined, {data: data, xhr: xhr});
    }

    /**
     * Отправка запроса на сервер
     *
     * @param object requestOptions
     * @since 2.0.0
     * @version 2.0.0
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

    public success(data: any, textStatus: string, xhr: any): void {
        let request: RequestClass = this;
        this.trigger('beforeSuccess', undefined, {request: request, data: data, textStatus: textStatus, xhr: xhr});
        if (!this.props('returnTransfer')) {
            if (data["error"] !== null) {
                let messenger: any = this.props('messenger');
                if (typeof messenger === "object") {
                    messenger.log(`Application error:\n${data.error.text}\n${data.error.file} [${data.error.line}]\n${data.error.trace}`);
                }
                this.responseError(data, xhr);
            } else {
                App.changeContent(data.binds);
                this.responseSuccess(data, xhr);
            }
            this.trigger('afterSuccess', undefined, {request: request, data: data, textStatus: textStatus, xhr: xhr});
        } else {
            this.trigger('afterSuccess', undefined, {request: request, data: data, textStatus: textStatus, xhr: xhr});
            return data;
        }
    }

    public init(properties: any): void {
        super.init(properties);
    }
}

$(document).ready(function () {
    //App.appendComponent('request', (properties: any, jq?: any) => new RequestClass(properties, jq));
    AppClass.prototype.request = (properties: any, jq?: any) => new RequestClass(properties, jq);
});
