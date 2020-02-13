var Core = (function () {
    function Core() {
    }
    Core.app = function (app) {
        if (app === void 0) { app = undefined; }
        if (app !== undefined) {
            Core._app = app;
            return Core._app;
        }
        return Core._app;
    };
    Core._app = undefined;
    return Core;
}());
//# sourceMappingURL=core.js.map