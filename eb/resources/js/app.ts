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
        onRequestError: [],
        onResponseSuccess: [],
        onResponseError: []
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
        $(window).on('resize', function (event: Event) {
            app.resize(event);
        });
        this.on('requestError', function(sender: any, xhr?: JQueryXHR, params: any = {}) {
            app.requestError(xhr);
        });
        this.on('responseSuccess', function (sender: any, xhr?: JQueryXHR, params: any = {}): void {
            app.changeContent(params);
        });
        super.init(properties);
    }

    /**
     * Обработчик ошибок после запроса (HTTP вернул не 200 OK)
     *
     * @param JQueryXHR xhr
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public requestError(xhr: JQueryXHR): void {
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
