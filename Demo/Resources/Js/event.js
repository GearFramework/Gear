var GEvent = (function () {
    function GEvent(sender, target, properties) {
        if (target === void 0) { target = undefined; }
        if (properties === void 0) { properties = undefined; }
        this.sender = sender;
        this.target = target !== undefined ? target : sender;
        this.properties = properties;
    }
    Object.defineProperty(GEvent.prototype, "properties", {
        get: function () {
            return this._properties;
        },
        set: function (properties) {
            this._properties = properties;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GEvent.prototype, "sender", {
        get: function () {
            return this._sender;
        },
        set: function (sender) {
            this._sender = sender;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(GEvent.prototype, "target", {
        get: function () {
            return this._target;
        },
        set: function (target) {
            this._target = target;
        },
        enumerable: true,
        configurable: true
    });
    return GEvent;
}());
//# sourceMappingURL=event.js.map