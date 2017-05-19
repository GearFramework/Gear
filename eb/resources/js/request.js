var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var RequestClass = (function (_super) {
    __extends(RequestClass, _super);
    function RequestClass(properties, jq) {
        if (properties === void 0) { properties = {}; }
        _super.call(this, properties, jq);
        this._propertiesDefault = {
            messenger: console,
            progress: null,
            dataType: 'json',
            method: "GET",
            onInit: [],
            onResponseSuccess: [
                function (sender, event, params) {
                    params.sender = sender;
                    App.trigger('responseSuccess', event, params);
                }
            ],
            onResponseError: [
                function (sender, event, params) {
                    console.log('App response error');
                    params.sender = sender;
                    App.trigger('responseError', event, params);
                }
            ],
            onRequestComplete: [
                function (sender, event, params) {
                    params.sender = sender;
                    App.trigger('requestComplete', event, params);
                }
            ],
            onRequestError: [
                function (sender, event, params) {
                    params.sender = sender;
                    App.trigger('requestError', event, params);
                }
            ]
        };
        this._ajaxFields = [
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
        this.props(this._mergeProperties(this._propertiesDefault, properties));
        this.init(properties);
    }
    Object.defineProperty(RequestClass.prototype, "ajaxFields", {
        get: function () {
            return this._ajaxFields;
        },
        enumerable: true,
        configurable: true
    });
    RequestClass.prototype.beforeSend = function (xhr, settings) {
        var progress = this.props('progress');
        if (progress !== null) {
            progress.start();
        }
        this.trigger('beforeSend', xhr, { setting: settings });
    };
    RequestClass.prototype.complete = function (xhr, textStatus) {
        var progress = this.props('progress');
        if (progress !== null) {
            progress.stop().reset();
        }
        this.trigger('requestComplete', xhr, { textStatus: textStatus });
    };
    RequestClass.prototype.error = function (xhr, status, errorMessage) {
        var messenger = this.props('messenger');
        if (messenger !== null) {
            messenger.log("Request error [" + xhr.status + "] " + xhr.statusText);
        }
        this.trigger('requestError', xhr, { status: status, errorMessage: errorMessage });
    };
    RequestClass.prototype.get = function (requestOptions) {
        if (requestOptions === void 0) { requestOptions = { method: "GET" }; }
        if (requestOptions["method"] === undefined) {
            requestOptions["method"] = "GET";
        }
        this.send(requestOptions);
    };
    RequestClass.prototype.init = function (properties) {
        if (properties === void 0) { properties = {}; }
        _super.prototype.init.call(this, properties);
    };
    RequestClass.prototype.post = function (requestOptions) {
        if (requestOptions === void 0) { requestOptions = { method: "POST" }; }
        if (requestOptions["method"] === undefined) {
            requestOptions["method"] = "POST";
        }
        this.send(requestOptions);
    };
    RequestClass.prototype.responseError = function (data, xhr) {
        this.trigger('responseError', xhr, data);
    };
    RequestClass.prototype.responseSuccess = function (data, xhr) {
        this.trigger('responseSuccess', xhr, data);
    };
    RequestClass.prototype.send = function (requestOptions) {
        if (requestOptions === void 0) { requestOptions = {}; }
        this.props(requestOptions);
        var request = this;
        var options = {};
        var ajax = this.ajaxFields;
        for (var _i = 0, ajax_1 = ajax; _i < ajax_1.length; _i++) {
            var field = ajax_1[_i];
            if (this.properties[field] !== undefined) {
                options[field] = this.properties[field];
            }
        }
        options.beforeSend = function (xhr, settings) { return request.beforeSend(xhr, settings); };
        options.success = function (data, textStatus, xhr) { return request.success(data, textStatus, xhr); };
        options.error = function (xhr, status, errorMessage) { return request.error(xhr, status, errorMessage); };
        options.complete = function (xhr, textStatus) { return request.complete(xhr, textStatus); };
        $.ajax(options);
    };
    RequestClass.prototype.success = function (data, textStatus, xhr) {
        var request = this;
        this.trigger('beforeSuccess', xhr, { request: request, data: data, textStatus: textStatus });
        if (!this.props('returnTransfer')) {
            if (data["error"] !== undefined) {
                var messenger = this.props('messenger');
                if (messenger !== null) {
                    messenger.log("Application error:\n" + data + "\n" + data.error.file + " [" + data.error.line + "]\n" + data.error.trace);
                }
                this.responseError(data, xhr);
            }
            else {
                this.responseSuccess(data, xhr);
            }
            this.trigger('afterSuccess', xhr, { request: request, data: data, textStatus: textStatus });
        }
        else {
            this.trigger('afterSuccess', xhr, { request: request, data: data, textStatus: textStatus });
        }
    };
    return RequestClass;
}(ObjectClass));
$(document).ready(function () {
    AppClass.prototype.request = function (properties, jq) { return new RequestClass(properties, jq); };
});
//# sourceMappingURL=request.js.map