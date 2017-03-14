/**
 * Класс запросов
 *
 * @param _options
 * @returns {PanelClass}
 * @constructor
 * @since 08.06.2015
 */
function RequestClass() {
    /* Устанавливаем свойства по-умолчанию */
    this.properties = {
        url: '',
        type: 'GET',
        dataType: 'json',
        data: {},
        processData: true,
        progressbar: undefined,
        messenger: console,
        returnTransfer: false,
        onInit : [],
        onBeforeSend: [],
        onAfterSend: [],
        onBeforeSuccess: [],
        onAfterSuccess: [],
        onComplete: [],
        onError: []
    };
    /* вызываем родительский конструктор */
    return ObjectClass.apply(this, arguments);
}
/* Наследуемся от ObjectClass */
RequestClass.prototype = Object.create(ObjectClass.prototype);
RequestClass.prototype.constructor = RequestClass;

RequestClass.prototype.get = function(query) {
    query === undefined ? query = {type: 'GET'} : query.type = 'GET';
    this.send(query);
};

RequestClass.prototype.post = function(query) {
    query === undefined ? query = {type: 'POST'} : query.type = 'POST';
    if (query.data === undefined) {
        query.data = this.props('data');
    }
    this.send(query);
};

RequestClass.prototype.send = function(query) {
    if (query.url === undefined)
        query.url = this.properties.url;
    if (query.type === undefined)
        query.type = this.properties.type;
    if (query.dataType === undefined)
        query.dataType = this.properties.dataType;
    if (query.processData === undefined)
        query.processData = this.properties.processData;
    if (query.onBeforeSend !== undefined)
        this.on('beforeSend', query.onBeforeSend, true);
    if (query.onAfterSend !== undefined)
        this.on('afterSend', query.onAfterSend, true);
    if (query.onBeforeSuccess !== undefined)
        this.on('beforeSuccess', query.onBeforeSuccess, true);
    if (query.onAfterSuccess !== undefined)
        this.on('afterSuccess', query.onAfterSuccess, true);
    if (query.onComplete !== undefined)
        this.on('complete', query.onComplete, true);
    if (query.onError !== undefined)
        this.on('error', query.onError, true);
    var request = this;
    query.beforeSend = function(xhr, settings) { request.beforeSend(xhr, settings); };
    query.success = function(data, textStatus, xhr) { request.success(data, textStatus, xhr); };
    query.error = function(xhr, status, errorMessage) { request.error(xhr, status, errorMessage); };
    query.complete = function(xhr, textStatus) { request.complete(xhr, textStatus); };
    $.ajax(query);
};

RequestClass.prototype.beforeSend = function(xhr, settings) {
    var request = this;
    if (this.props('progressbar'))
        this.props('progressbar').start();
    this.trigger('beforeSend', undefined, {request: request, xhr: xhr, settings: settings});
};

RequestClass.prototype.success = function(data, textStatus, xhr) {
    var request = this;
    this.trigger('beforeSuccess', undefined, {request: request, data: data, textStatus: textStatus, xhr: xhr});
    if (!this.props('returnTransfer')) {
        if (data.error) {
            if (this.props('messenger') !== undefined) {
                this.props('messenger').log('ERROR# ' + data.error.text + "\n" + data.error.file + '[' + data.error.line + ']' + "\n" + data.error.trace);
            }
            this.onResponseError(data);
        } else {
            if (data.binds) {
                App.changeContent(data.binds);
            }
            this.onResponseSuccess(data);
        }
        this.trigger('afterSuccess', undefined, {request: request, data: data, textStatus: textStatus, xhr: xhr});
    } else {
        this.trigger('afterSuccess', undefined, {request: request, data: data, textStatus: textStatus, xhr: xhr});
        return data;
    }
};

RequestClass.prototype.error = function(xhr, status, errorMessage) {
    var request = this;
    if (this.props('messenger') !== undefined) {
        this.props('messenger').log('ERROR# [' + xhr.status + '] ' + xhr.statusText);
    }
    this.trigger('error', undefined, {request: request, xhr: xhr, status: status, errorMessage: errorMessage});
};

RequestClass.prototype.complete = function(xhr, textStatus) {
    var request = this;
    if (this.props('progressbar') !== undefined) {
        this.props('progressbar').stop();
    }
    this.trigger('complete', undefined, {request: request, xhr: xhr, textStatus: textStatus});
};

RequestClass.prototype.onResponseError = function(data) {
    this.trigger('responseError', undefined, {data: data});
};

RequestClass.prototype.onResponseSuccess = function(data) {
    this.trigger('responseSuccess', undefined, {data: data});
};

$(document).ready(function() {
    AppClass.prototype.request = function(query) {
        return new RequestClass(query);
    };
});
