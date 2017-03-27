var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var AppClass = (function (_super) {
    __extends(AppClass, _super);
    function AppClass() {
        _super.apply(this, arguments);
        this.properties = {
            controllers: {
                auth: ''
            },
            errorsHandlers: {
                401: function () {
                    window.location.href = "/" + App.props('controllers').auth;
                },
                403: function () {
                    window.location.href = "/" + App.props('controllers').denied;
                }
            },
            onInit: [],
            onErrorResponse: [function (sender, xhr, params) {
                    App.responseError(xhr);
                }]
        };
    }
    AppClass.prototype.appendComponent = function (name, component) {
        this._components[name] = component;
    };
    AppClass.prototype.init = function (properties) {
        var app = this;
        $(window).on('resize', function (event) {
            app.resize(event);
        });
        _super.prototype.init.call(this, properties);
    };
    AppClass.prototype.resize = function (event) {
        this.trigger('resize', event, {});
    };
    AppClass.prototype.responseError = function (xhr) {
        if (this.properties.errorsHandlers[xhr.status] !== undefined) {
            this.properties.errorsHandlers[xhr.status]();
        }
    };
    return AppClass;
}(ObjectClass));
//# sourceMappingURL=app.js.map