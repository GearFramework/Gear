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
var GRequest = (function (_super) {
    __extends(GRequest, _super);
    function GRequest() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        _this._ajaxFields = [
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
        return _this;
    }
    Object.defineProperty(GRequest.prototype, "ajaxFields", {
        get: function () {
            return this._ajaxFields;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GRequest.prototype, "app", {
        get: function () {
            return Core.app();
        },
        enumerable: true,
        configurable: true
    });
    GRequest.prototype.beforeSend = function (xhr, settings) {
        var progress = this.properties.progress;
        if (progress !== null && typeof progress === "object") {
            progress.start();
        }
        this.trigger('beforeSend', xhr, { setting: settings });
    };
    GRequest.prototype.complete = function (xhr, textStatus) {
        var progress = this.props('progress');
        if (progress !== null && typeof progress === "object") {
            progress.stop();
        }
        this.trigger('requestComplete', xhr, { textStatus: textStatus });
    };
    GRequest.prototype.error = function (xhr, status, errorMessage) {
        if (this.properties.messenger !== undefined) {
            this.properties.messenger.log("Request error [" + xhr.status + "] " + xhr.statusText);
        }
        this.trigger('requestError', xhr, { status: status, errorMessage: errorMessage });
    };
    GRequest.prototype.get = function (requestOptions) {
        if (requestOptions === void 0) { requestOptions = { method: "GET" }; }
        if (requestOptions["method"] === undefined) {
            requestOptions["method"] = "GET";
        }
        this.send(requestOptions);
    };
    GRequest.prototype.init = function () {
        var _this = this;
        this.properties = {
            dataType: 'json',
            messenger: console,
            progress: undefined,
            requestOptions: {},
            onRequestError: [function (req, xhr, params) {
                    _this.app.trigger('requestError', xhr, params);
                }],
            onRequestCompletes: [function (req, xhr, params) {
                    _this.app.trigger('requestComplete', xhr, params);
                }]
        };
        return this;
    };
    GRequest.prototype.initDefaultProperties = function () {
        var _this = this;
        _super.prototype.initDefaultProperties.call(this);
        this.properties.dataType = 'json';
        this.properties.messenger = console;
        this.properties.progress = undefined;
        this.properties.requestOptions = {};
        this.properties.onRequestError = [function (req, xhr, response) {
                _this.app.trigger('requestError', xhr, response);
            }];
        this.properties.onRequestCompletes = [function (req, xhr, response) {
                _this.app.trigger('requestComplete', xhr, response);
            }];
        return this;
    };
    GRequest.prototype.onAfterConstruct = function (event, params) {
    };
    GRequest.prototype.post = function (requestOptions) {
        if (requestOptions === void 0) { requestOptions = { method: "POST" }; }
        if (requestOptions["method"] === undefined) {
            requestOptions["method"] = "POST";
        }
        this.send(requestOptions);
    };
    GRequest.prototype.responseError = function (data, xhr) {
        if (this.properties.messenger !== undefined) {
            if (data.error instanceof Array) {
                for (var idx in data.error) {
                    var error = data.error[idx];
                    this.properties.messenger.log("Application error:\n" + error.text + "\n" + error.file + " [" + error.line + "]\n" + error.trace);
                }
            }
            else {
                this.properties.messenger.log("Application error:\n" + data.error.text + "\n" + data.error.file + " [" + data.error.line + "]\n" + data.error.trace);
            }
        }
        this.app.trigger('responseError', xhr, { data: data });
    };
    GRequest.prototype.responseSuccess = function (data, xhr) {
        this.trigger('responseSuccess', xhr, { data: data });
    };
    GRequest.prototype.send = function (requestOptions) {
        var _this = this;
        var options = this.mergeProperties(requestOptions);
        this.properties = options;
        var ajax = this.ajaxFields;
        for (var _i = 0, ajax_1 = ajax; _i < ajax_1.length; _i++) {
            var field = ajax_1[_i];
            if (this.properties[field] !== undefined) {
                options[field] = this.properties[field];
            }
        }
        options.beforeSend = function (xhr, settings) {
            _this.beforeSend(xhr, settings);
        };
        options.success = function (data, textStatus, xhr) {
            _this.success(data, textStatus, xhr);
        };
        options.error = function (xhr, status, errorMessage) {
            _this.error(xhr, status, errorMessage);
        };
        options.complete = function (xhr, textStatus) {
            _this.complete(xhr, textStatus);
        };
        $.ajax(options);
    };
    GRequest.prototype.success = function (data, textStatus, xhr) {
        var request = this;
        this.trigger('beforeSuccess', xhr, { request: this, data: data, textStatus: textStatus });
        if (!this.properties.returnTransfer) {
            if (data.status != 200) {
                this.responseError(data, xhr);
            }
            else {
                this.responseSuccess(data, xhr);
            }
            this.trigger('requestSuccess', xhr, data);
        }
    };
    return GRequest;
}(GObject));
//# sourceMappingURL=request.js.map