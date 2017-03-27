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
            onInit: []
        };
    }
    RequestClass.prototype.beforeSend = function (xhr, settings) {
        var progress = this.props('progress');
        if (typeof progress === "object") {
            progress.start();
        }
        App.trigger('beforeSend', xhr, { setting: settings });
    };
    RequestClass.prototype.complete = function (xhr, textStatus) {
        var progress = this.props('progress');
        if (typeof progress === "object") {
            progress.stop().reset();
        }
        App.trigger('complete', xhr, { textStatus: textStatus });
    };
    RequestClass.prototype.error = function (xhr, status, errorMessage) {
        var messenger = this.props('messenger');
        if (typeof messenger === "object") {
            messenger.log("Request error [" + xhr.status + "] " + xhr.statusText);
        }
        App.trigger('errorResponse', xhr, { status: status, errorMessage: errorMessage });
    };
    RequestClass.prototype.get = function (requestOptions) {
        if (requestOptions === void 0) { requestOptions = {}; }
        typeof requestOptions === "undefined" ? requestOptions = { method: "GET" } : requestOptions["method"] = "GET";
    };
    RequestClass.prototype.post = function (requestOptions) {
        if (requestOptions === void 0) { requestOptions = {}; }
        typeof requestOptions === "undefined" ? requestOptions = { method: "POST" } : requestOptions["method"] = "POST";
    };
    RequestClass.prototype.responseError = function (data, xhr) {
        this.trigger('responseError', undefined, { data: data, xhr: xhr });
    };
    RequestClass.prototype.responseSuccess = function (data, xhr) {
        this.trigger('responseSuccess', undefined, { data: data, xhr: xhr });
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
        this.trigger('beforeSuccess', undefined, { request: request, data: data, textStatus: textStatus, xhr: xhr });
        if (!this.props('returnTransfer')) {
            if (data["error"] !== null) {
                var messenger = this.props('messenger');
                if (typeof messenger === "object") {
                    messenger.log("Application error:\n" + data.error.text + "\n" + data.error.file + " [" + data.error.line + "]\n" + data.error.trace);
                }
                this.responseError(data, xhr);
            }
            else {
                App.changeContent(data.binds);
                this.responseSuccess(data, xhr);
            }
            this.trigger('afterSuccess', undefined, { request: request, data: data, textStatus: textStatus, xhr: xhr });
        }
        else {
            this.trigger('afterSuccess', undefined, { request: request, data: data, textStatus: textStatus, xhr: xhr });
            return data;
        }
    };
    RequestClass.prototype.init = function (properties) {
        _super.prototype.init.call(this, properties);
    };
    return RequestClass;
}(ObjectClass));
$(document).ready(function () {
    AppClass.prototype.request = function (properties, jq) { return new RequestClass(properties, jq); };
});
//# sourceMappingURL=request.js.map