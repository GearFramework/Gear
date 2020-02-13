var GObject = (function () {
    function GObject(properties, jq) {
        this.jq = jq;
        this.initDefaultProperties();
        this.properties = this.mergeProperties(properties);
        this.afterConstruct();
    }
    Object.defineProperty(GObject.prototype, "jq", {
        get: function () {
            return this._jq;
        },
        set: function (jq) {
            this._jq = jq;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GObject.prototype, "properties", {
        get: function () {
            return this._properties;
        },
        set: function (props) {
            this._properties = props;
        },
        enumerable: true,
        configurable: true
    });
    GObject.prototype.afterChangeBind = function (bindName, bind, dataBindElement) {
        return this.trigger('afterChangeBind', new GEvent(this), {
            bindName: bindName,
            bind: bind,
            bindElement: dataBindElement
        });
    };
    GObject.prototype.afterChangeContent = function (target) {
        return this.trigger('afterChangeContent', new GEvent(this, target), {});
    };
    GObject.prototype.afterConstruct = function () {
        return this.trigger('afterConstruct', new GEvent(this, this), {});
    };
    GObject.prototype.beforeChangeBind = function (bindName, bind, dataBindElement) {
        return this.trigger('beforeChangeBind', new GEvent(this), {
            bindName: bindName,
            bind: bind,
            bindElement: dataBindElement
        });
    };
    GObject.prototype.beforeChangeContent = function (target) {
        return this.trigger('beforeChangeContent', new GEvent(this, target), {});
    };
    GObject.prototype.initDefaultProperties = function () {
        this.properties = {
            onConstruct: [],
        };
        return this;
    };
    GObject.prototype.mergeProperties = function (constructProperties) {
        var objectProperties = this.properties;
        var value = undefined;
        for (var name_1 in constructProperties) {
            value = constructProperties[name_1];
            if (name_1.match(/^on[A-Z]/)) {
                if (typeof value === 'function') {
                    value = [value];
                }
                if (objectProperties[name_1] == undefined) {
                    objectProperties[name_1] = value;
                }
                else {
                    var i = void 0;
                    for (i in value) {
                        objectProperties[name_1].push(value[i]);
                    }
                }
            }
            else {
                objectProperties[name_1] = value;
            }
        }
        return objectProperties;
    };
    GObject.prototype.off = function (eventName, eventHandler) {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName]) {
            if (!eventHandler) {
                this.properties[eventName] = [];
            }
            else {
                for (var i in this.properties[eventName]) {
                    if (this.properties[eventName] === eventHandler) {
                        delete this.properties[eventName][i];
                        break;
                    }
                }
            }
        }
    };
    GObject.prototype.on = function (eventName, eventHandler) {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName]) {
            this.properties[eventName].push(eventHandler);
        }
        else {
            this.properties[eventName] = [eventHandler];
        }
    };
    GObject.prototype.prepareEventName = function (eventName) {
        if (!eventName.match(/^on[A-Z]/)) {
            eventName = 'on' + eventName.charAt(0).toUpperCase() + eventName.substr(1);
        }
        return eventName;
    };
    GObject.prototype.props = function (name, value) {
        var result = undefined;
        if (name !== undefined) {
            if (typeof name === "object") {
                var nameProp = void 0;
                var valueProps = void 0;
                for (nameProp in name) {
                    this.props(nameProp, name[nameProp]);
                }
            }
            else if (typeof name === "string") {
                if (value === undefined) {
                    result = this.properties[name];
                }
                else {
                    if (name.match('^on[A-Z]')) {
                        if (typeof value === "function") {
                            this.on(name, value);
                        }
                        else {
                            var handler = void 0;
                            for (var _i = 0, value_1 = value; _i < value_1.length; _i++) {
                                handler = value_1[_i];
                                this.props(name, handler);
                            }
                        }
                    }
                    else {
                        this.properties[name] = value;
                    }
                }
            }
        }
        else {
            result = this.properties;
        }
        return result;
    };
    GObject.prototype.trigger = function (eventName, event, params) {
        var result = true;
        var lastResult = result;
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName] !== undefined) {
            var i = void 0;
            for (i in this.properties[eventName]) {
                result = this.properties[eventName][i](this, event, params);
                if (lastResult === true && result !== undefined && result !== null) {
                    lastResult = result;
                }
            }
        }
        return lastResult;
    };
    return GObject;
}());
//# sourceMappingURL=object.js.map