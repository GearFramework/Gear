var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var RequestClass = (function (_super) {
    __extends(RequestClass, _super);
    function RequestClass() {
        _super.apply(this, arguments);
        this.properties = {
            messenger: console,
            progress: null,
            requestOptions: {},
            onInit: [],
            onResponseSuccess: [
                function (eventName, event, params) {
                    App.trigger('responseSuccess', event, params);
                }
            ],
            onResponseError: [
                function (eventName, event, params) {
                    App.trigger('responseError', event, params);
                }
            ],
            onRequestComplete: [
                function (eventName, event, params) {
                    App.trigger('requestComplete', event, params);
                }
            ],
            onRequestError: [
                function (eventName, event, params) {
                    App.trigger('requestError', event, params);
                }
            ]
        };
    }
    RequestClass.prototype.beforeSend = function (xhr, settings) {
        var progress = this.props('progress');
        if (typeof progress === "object") {
            progress.start();
        }
        this.trigger('beforeSend', xhr, { setting: settings });
    };
    RequestClass.prototype.complete = function (xhr, textStatus) {
        var progress = this.props('progress');
        if (typeof progress === "object") {
            progress.stop().reset();
        }
        this.trigger('requestComplete', xhr, { textStatus: textStatus });
    };
    RequestClass.prototype.error = function (xhr, status, errorMessage) {
        var messenger = this.props('messenger');
        if (typeof messenger === "object") {
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
        this.trigger('responseError', xhr, { data: data });
    };
    RequestClass.prototype.responseSuccess = function (data, xhr) {
        this.trigger('responseSuccess', xhr, { data: data });
    };
    RequestClass.prototype.send = function (requestOptions) {
        var options = this.props('requestOptions');
        for (var name_1 in requestOptions) {
            options[name_1] = requestOptions[name_1];
        }
        options.beforeSend = this.beforeSend;
        options.success = this.success;
        options.error = this.error;
        options.complete = this.complete;
        $.ajax(options);
    };
    RequestClass.prototype.success = function (data, textStatus, xhr) {
        var request = this;
        this.trigger('beforeSuccess', xhr, { request: request, data: data, textStatus: textStatus });
        if (!this.props('returnTransfer')) {
            if (data["error"] !== null) {
                var messenger = this.props('messenger');
                if (typeof messenger === "object") {
                    messenger.log("Application error:\n" + data.error.text + "\n" + data.error.file + " [" + data.error.line + "]\n" + data.error.trace);
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