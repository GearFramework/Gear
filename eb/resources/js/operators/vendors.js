var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var VendorsClass = (function (_super) {
    __extends(VendorsClass, _super);
    function VendorsClass() {
        _super.apply(this, arguments);
    }
    VendorsClass.prototype.init = function (properties) {
        _super.prototype.init.call(this, properties);
    };
    return VendorsClass;
}(ObjectClass));
AppClass.prototype.vendors = new VendorsClass({
    navigator: new ToolbarClass({}, $('.vendors-navigator-panel')),
    toolbar: new ToolbarClass({
        buttons: {
            add: new ButtonClass({
                action: function () {
                    alert('add');
                }
            }, $('.vendors-toolbar-panel .button.add')),
            edit: new ButtonClass({
                action: function () {
                    alert('edit');
                }
            }, $('.vendors-toolbar-panel .button.edit'))
        }
    }, $('.vendors-toolbar-panel'))
}, $('.vendors-list'));
//# sourceMappingURL=vendors.js.map