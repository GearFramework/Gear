var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var MessagesClass = (function (_super) {
    __extends(MessagesClass, _super);
    function MessagesClass() {
        _super.apply(this, arguments);
        this.properties = {
            notifyContainer: null,
            shadow: null,
            onShow: [],
            onClose: []
        };
    }
    MessagesClass.prototype.showMessage = function (message) {
    };
    MessagesClass.prototype.showNotify = function (message) {
    };
    return MessagesClass;
}(ObjectClass));
$(document).ready(function () {
    AppClass.prototype.messages = function (properties, jq) { return new MessagesClass(properties, jq); };
});
//# sourceMappingURL=messages.js.map