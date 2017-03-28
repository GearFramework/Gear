/**
 * Класс приложения
 *
 * @since 0.0.1
 * @version 0.0.1
 */
class AppClass extends ObjectClass {
    /* Private */
    private _components: any;
    /* Protected */
    /* Public */
    public properties: any = {
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
        onErrorResponse: [function(sender: any, xhr: any, params: any) {
            App.responseError(xhr);
        }]
    };
    public request: any;
    public message: any;
    public progress: any;
    public vendors: any;

    public appendComponent(name: string, component: any): void {
        this._components[name] = component;
    }

    /**
     * Инициализация приложения
     *
     * @param properties
     * @since 0.0.1
     * @version 0.0.1
     */
    public init(properties: any): void {
        let app: AppClass = this;
        $(window).on('resize', function (event: any) {
            app.resize(event);
        });
        super.init(properties);
    }

    public resize(event: any): void {
        this.trigger('resize', event, {});
    }

    public responseError(xhr: any): void {
        if (this.properties.errorsHandlers[xhr.status] !== undefined) {
            this.properties.errorsHandlers[xhr.status]();
        }
    }
}

declare let App: AppClass;
