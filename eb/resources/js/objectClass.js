/**
 * Базовый класс
 *
 * @param object properties
 * @param object jq
 * @constructor
 */
function ObjectClass(properties, jq) {
    return this.init(properties, jq);
}

/**
 * Инициализация объекта
 *
 * @param object properties
 * @param object jq
 * @returns {ObjectClass}
 */
ObjectClass.prototype.init = function(properties, jq) {
    this.options(properties);
    this.jq = jq;
    this.onInit();
    return this;
};

/**
 * Установка обработчика на указанное событие
 *
 * @param name
 * @param callback
 * @param reset
 * @returns {ObjectClass}
 */
ObjectClass.prototype.on = function(name, callback, reset) {
    if (!name.match(/^on[A-Z]]/)) {
        name = 'on' + name.charAt(0).toUpperCase() + name.substr(1);
    }
    this.properties[name] === undefined || reset === true ? this.properties[name] = [callback] : this.properties[name].push(callback);
    return this;
};

/**
 * Генерация события
 *
 * @param name
 * @param event
 * @param params
 * @returns {ObjectClass}
 */
ObjectClass.prototype.trigger = function(name, event, params) {
    if (!name.match(/^on[A-Z]]/)) {
        name = 'on' + name.charAt(0).toUpperCase() + name.substr(1);
    }
    if (this.properties[name] !== undefined) {
        for(var i in this.properties[name]) {
            this.properties[name][i](this, event, params);
        }
    }
    return this;
};

/**
 * Возвращает true, если в текущем контексте существует указанный бинд
 *
 * @param string name
 * @returns {boolean}
 */
ObjectClass.prototype.isPossibleBind = function(name) {
    var possible = false;
    if (this.jq !== undefined) {
        if (this.jq.attr('bind') && this.jq.attr('bind') === name)
            possible = true;
        else
        if (this.jq.find('[bind="' + name + '"]').length)
            possible = true;
    }
    return possible;
};

/**
 * Изменени контента согласно биндингам
 *
 * @param string name
 * @param string content
 */
ObjectClass.prototype.changeContent = function(bindName, content) {
    if (this.isPossibleBind(bindName)) {
        this.onBeforeChangeContent(bindName, content);
        this.setContent(bindName, content);
        this.onAfterChangeContent(bindName, content);
    }
};

/**
 * Запись контента согласно биндингам
 *
 * @param string name
 * @param string content
 * @returns {ObjectClass}
 */
ObjectClass.prototype.setContent = function(name, content) {
    if (this.jq.attr('bind') && this.jq.attr('bind') === name)
        this.jq.html(content);
    else
        this.jq.find('[bind="' + name + '"]').html(content);
    return this;
};

/**
 * Обработка исключений
 *
 * @param object exception
 */
ObjectClass.prototype.prepareException = function(exception) {
    this.properties.messenger !== undefined ? this.properties.messenger.show(exception.message) : console.log(exception.message);
};

/**
 * Установка/получение параметров(свойств)
 *
 * @param name
 * @param value
 * @returns {mixed}
 */
ObjectClass.prototype.options = function(name, value) {
    var returnValue = undefined;
    if (name === undefined)
        returnValue = this.properties;
    else {
        if (typeof name === 'object') {
            for(var k in name) {
                k.match(/^on[A-Z]/) ? this.properties[k] = [name[k]] : this.properties[k] = name[k];
            }
        } else if (name !== undefined && value === undefined) {
            returnValue = this.properties[name];
        } else if (name !== undefined && value !== undefined) {
            this.properties[name] = value;
            returnValue = this;
        }
    }
    return returnValue;
};

/**
 * Установка/получение параметров(свойств)
 *
 * @param name
 * @param value
 * @returns {mixed}
 */
ObjectClass.prototype.props = function(name, value) {
    return this.options(name, value);
};

/**
 * Событие возникающее во время инициализации объекта
 *
 * @param object event
 */
ObjectClass.prototype.onInit = function(event) {
    this.trigger('init', event);
};

/**
 * Событие возникающее перед изменением контента
 *
 * @param string bindName
 * @param string content
 */
ObjectClass.prototype.onBeforeChangeContent = function(bindName, content) {
    this.trigger('beforeChangeContent', undefined, {bindname: bindName, content: content});
};

/**
 * Событие возникающее после изменения контента
 *
 * @param string bindName
 * @param string content
 */
ObjectClass.prototype.onAfterChangeContent = function(bindName, content) {
    this.trigger('afterChangeContent', undefined, {bindname: bindName, content: content});
};
