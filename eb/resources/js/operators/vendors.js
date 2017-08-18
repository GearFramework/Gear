var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var VendorsClass = (function (_super) {
    __extends(VendorsClass, _super);
    function VendorsClass(properties, jq) {
        if (properties === void 0) { properties = {}; }
        _super.call(this, properties, jq);
        this.props(this._mergeProperties(this._propertiesDefault, properties));
        this.init(properties);
    }
    VendorsClass.prototype.getSelectedVendor = function () {
        return this.jq.find('.vendor-tile.selected');
    };
    VendorsClass.prototype.init = function (properties) {
        var _this = this;
        var vendors = this;
        $('.wrapper>.page-wrapper').css('margin-left', this.jq.parent().width());
        this.jq.find('.vendor-tile').on('click', function (event) { return _this.selectVendor($(event.currentTarget)); });
        App.on('resize', function (sender, event, params) {
            var height = App.getHeight() - _this.jq.parent().offset().top;
            _this.jq.parent().height(height);
            $('.wrapper>.page-wrapper').height(height);
        });
        $(window).trigger('resize');
        _super.prototype.init.call(this, properties);
    };
    VendorsClass.prototype.selectVendor = function (vendor) {
        App.request({ url: vendor.attr('data-action'), onRequestComplete: function () { $(window).trigger('resize'); } }).get();
        this.getSelectedVendor().removeClass('selected');
        vendor.addClass('selected');
    };
    return VendorsClass;
}(ObjectClass));
var VendorOrdersClass = (function (_super) {
    __extends(VendorOrdersClass, _super);
    function VendorOrdersClass(properties, jq) {
        if (properties === void 0) { properties = {}; }
        _super.call(this, properties, jq);
        this.props(this._mergeProperties(this._propertiesDefault, properties));
        this.init(properties);
    }
    VendorOrdersClass.prototype.init = function (properties) {
        _super.prototype.init.call(this, properties);
    };
    return VendorOrdersClass;
}(ObjectClass));
var VendorCategoriesClass = (function (_super) {
    __extends(VendorCategoriesClass, _super);
    function VendorCategoriesClass(properties, jq) {
        if (properties === void 0) { properties = {}; }
        _super.call(this, properties, jq);
        this.props(this._mergeProperties(this._propertiesDefault, properties));
        this.init(properties);
    }
    VendorCategoriesClass.prototype.init = function (properties) {
        _super.prototype.init.call(this, properties);
    };
    return VendorCategoriesClass;
}(ObjectClass));
App.vendors = new VendorsClass({
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