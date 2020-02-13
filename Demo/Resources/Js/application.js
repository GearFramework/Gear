var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var GApplication = (function (_super) {
    __extends(GApplication, _super);
    function GApplication() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    Object.defineProperty(GApplication.prototype, "bootstrap", {
        get: function () {
            return this._bootstrap;
        },
        set: function (bs) {
            this._bootstrap = bs;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GApplication.prototype, "controllers", {
        get: function () {
            return this._controllers;
        },
        set: function (controllers) {
            this._controllers = controllers;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GApplication.prototype, "height", {
        get: function () {
            return parseInt($(window).attr('innerHeight'));
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GApplication.prototype, "lastRequest", {
        get: function () {
            return this.request;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GApplication.prototype, "request", {
        get: function () {
            this._request = new GRequest({ app: this });
            return this._request;
        },
        set: function (request) {
            this._request = request;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GApplication.prototype, "width", {
        get: function () {
            return $(window).width();
        },
        enumerable: true,
        configurable: true
    });
    GApplication.prototype.changeContent = function (data, target) {
        var bindName;
        var bind;
        var dataBindElement;
        if (target === undefined) {
            target = this;
        }
        if (target.jq !== undefined) {
            this.beforeChangeContent(target);
            for (bindName in data) {
                bind = data[bindName];
                if (target.jq.attr('data-bind') === bindName) {
                    dataBindElement = target.jq;
                }
                else {
                    dataBindElement = target.jq.find("[data-bind=\"" + bindName + "\"]");
                    if (dataBindElement.length == 0) {
                        return;
                    }
                }
                target.beforeChangeBind(bindName, bind, dataBindElement);
                if (bind.options !== undefined) {
                    if (bind.options.append) {
                        dataBindElement.append(bind.content);
                    }
                    else if (bind.options.prepend) {
                        dataBindElement.prepend(bind.content);
                    }
                }
                else {
                    dataBindElement.html(bind);
                }
                target.afterChangeBind(bindName, bind, dataBindElement);
            }
            this.afterChangeContent(target);
            $(window).trigger('resize');
        }
    };
    GApplication.prototype.onAfterCostruct = function () {
        var _this = this;
        var app = this;
        $(window).on('resize', function (event) {
            _this.resize(event);
        });
        this.on('requestError', function (sender, xhr, params) {
            if (params === void 0) { params = {}; }
            _this.requestError(xhr);
        });
        this.on('requestSuccess', function (sender, xhr, params) {
            if (params === void 0) { params = {}; }
            _this.requestSuccess(params);
        });
        this.on('responseError', function (sender, xhr, params) {
            if (params === void 0) { params = {}; }
            _this.responseError(params.data.status);
        });
        this.on('beforeChangeContent', function (sender, xhr, params) {
            if (params === void 0) { params = {}; }
            params.target.trigger('beforeChangeContent');
        });
        this.on('afterChangeContent', function (sender, xhr, params) {
            if (params === void 0) { params = {}; }
            params.target.trigger('afterChangeContent');
        });
        return true;
    };
    GApplication.prototype.initDefaultProperties = function () {
        var app;
        this.properties = {
            controllers: {
                auth: 'user/auth',
                denied: 'user/denied'
            },
            requestErrorHandlers: {
                401: function () {
                    window.location.href = "?r=" + app.props('controllers').auth;
                },
                403: function () {
                    window.location.href = "?r=" + app.props('controllers').denied;
                }
            },
            onInit: [],
            onConstruct: [],
        };
        return this;
    };
    GApplication.prototype.requestError = function (xhr) {
        if (this.properties.requestErrorHandlers[xhr.status] !== undefined) {
            this.properties.requestErrorHandlers[xhr.status]();
        }
    };
    GApplication.prototype.requestSuccess = function (params) {
        this.changeContent(undefined, params.binds !== undefined ? params.binds : {});
    };
    GApplication.prototype.responseError = function (status) {
        if (this.properties.requestErrorHandlers[status] !== undefined) {
            this.properties.requestErrorHandlers[status]();
        }
    };
    GApplication.prototype.resize = function (event) {
        var padding = parseInt($('.content-container').css('padding-top')) + parseInt($('.content-container').css('padding-bottom'));
        $('.content-container').height(this.height - $('.content-container').offset().top - padding);
        this.trigger('resize', event, {});
    };
    GApplication.prototype.run = function () {
        this.bootstrap();
    };
    return GApplication;
}(GObject));
//# sourceMappingURL=application.js.map