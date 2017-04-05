var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var ControlsClass = (function (_super) {
    __extends(ControlsClass, _super);
    function ControlsClass() {
        _super.apply(this, arguments);
        this.controls = null;
    }
    ControlsClass.prototype.checkAppendControls = function (sender, event, params) {
    };
    ControlsClass.prototype.init = function (properties) {
        if (properties === void 0) { properties = {}; }
        App.on('beforeChangeContent', this.checkAppendControls);
        this.loadControls();
        _super.prototype.init.call(this, properties);
    };
    ControlsClass.prototype.loadControls = function () {
        this.controls = [];
        $('.controls.button, .controls.radio, .controls.checkbox').each(function (key, control) {
            control = $(control);
            if (control.hasClass('button')) {
                this.controls.push(new ButtonClass({}, control));
            }
            else if (control.hasClass('checkbox')) {
                this.controls.push(new CheckButtonClass({}, control));
            }
            else if (control.hasClass('readio')) {
                this.controls.push(new RadioButtonClass({}, control));
            }
        });
    };
    return ControlsClass;
}(ObjectClass));
var ButtonClass = (function (_super) {
    __extends(ButtonClass, _super);
    function ButtonClass() {
        _super.apply(this, arguments);
    }
    ButtonClass.prototype.click = function (event) {
    };
    ButtonClass.prototype.init = function (properties) {
        this.action = this.jq.attr('data-action');
        var control = this;
        this.jq.on('click', function (event) {
            control.click(event);
        });
        _super.prototype.init.call(this, properties);
    };
    return ButtonClass;
}(ObjectClass));
var CheckButtonClass = (function (_super) {
    __extends(CheckButtonClass, _super);
    function CheckButtonClass() {
        _super.apply(this, arguments);
    }
    CheckButtonClass.prototype.init = function (properties) {
        var action = this.jq.attr('data-action');
        _super.prototype.init.call(this, properties);
    };
    return CheckButtonClass;
}(ButtonClass));
var RadioButtonClass = (function (_super) {
    __extends(RadioButtonClass, _super);
    function RadioButtonClass() {
        _super.apply(this, arguments);
    }
    RadioButtonClass.prototype.init = function (properties) {
        var action = this.jq.attr('data-action');
        _super.prototype.init.call(this, properties);
    };
    return RadioButtonClass;
}(ButtonClass));
var ToolbarClass = (function (_super) {
    __extends(ToolbarClass, _super);
    function ToolbarClass() {
        _super.apply(this, arguments);
    }
    ToolbarClass.prototype.init = function (properties) {
        _super.prototype.init.call(this, properties);
    };
    return ToolbarClass;
}(ObjectClass));
//# sourceMappingURL=controls.js.map