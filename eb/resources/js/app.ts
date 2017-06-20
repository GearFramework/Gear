/**
 * Класс приложения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @since 0.0.1
 * @version 0.0.1
 */
class AppClass extends ObjectClass {
    /* Private */
    /* Protected */
    protected _propertiesDefault: any = {
        controllers: {
            auth: ''
        },
        errorsHandlers: {
            401: function() {
                window.location.href = `/${App.props('controllers').auth}`;
            },
            403: function() {
                window.location.href = `/${App.props('controllers').denied}`;
            }
        },
        onInit: [],
        onRequestComplete: [],
        onRequestError: [(sender: Object, event?: any, params?: any): void => { App.requestError(event, params); }],
        onResize: [],
        onResponseSuccess: [],
        onResponseError: [(sender: Object, event?: any, params?: any): void => { App.responseError(event, params); }]
    };
    /* Public */
    public request: any;
    public message: any;
    public progress: any;
    public timer: TimerGenerator;
    public messages: any;
    public vendors: any;

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

    public getHeight(): number {
        let height: number = parseInt($(window).attr('innerHeight'));
        return height;
    }

    public getWidth(): number {
        let width: number = $(window).width();
        return width;
    }
    /**
     * Инициализация приложения
     *
     * @param properties
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public init(properties: any): void {
        let app: AppClass = this;
        $(window).on('resize', (event: Event): void => app.resize(event));
        this.on('requestError', (sender: any, xhr?: JQueryXHR, params: any = {}): void => app.requestError(xhr));
        this.on('responseSuccess', (sender: any, xhr?: JQueryXHR, params: any = {}): void => app.changeContent(params));
        this.on('resize', (sender: any, xhr?: JQueryXHR, params: any = {}): void => {
            this.jq.find('.top-fix').height(app.jq.find('.top').height())
        });
        super.init(properties);
    }

    /**
     * Обработчик ошибок после запроса (HTTP вернул не 200 OK)
     *
     * @param JQueryXHR xhr
     * @param any params
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public requestError(xhr: JQueryXHR, params?: any): void {
        if (this.properties.errorsHandlers[xhr.status] !== undefined) {
            this.properties.errorsHandlers[xhr.status]();
        }
    }

    /**
     * Обработчик ошибок после запроса (HTTP вернул не 200 OK)
     *
     * @param JQueryXHR xhr
     * @param any params
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public responseError(xhr: JQueryXHR, params?: any): void {
        console.log(xhr);
        if (this.properties.errorsHandlers[xhr.status] !== undefined) {
            this.properties.errorsHandlers[xhr.status]();
        }
    }

    /**
     * Обработчик события, вознкиающего при изменении окна браузера
     *
     * @param Event event
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public resize(event: Event): void {
        this.trigger('resize', event, {});
    }
}

declare let App: AppClass;
