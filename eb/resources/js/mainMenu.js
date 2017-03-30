var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var MainMenuClass = (function (_super) {
    __extends(MainMenuClass, _super);
    function MainMenuClass() {
        _super.apply(this, arguments);
    }
    MainMenuClass.prototype.deselectMenuItem = function (menuItem, event) {
        menuItem.removeClass('selected');
        this.hideSubmenu(menuItem);
        this.trigger('deselectMainMenuItem', event, { menuItem: menuItem });
    };
    MainMenuClass.prototype.getSelectedMenuItem = function () {
        var item = this.jq.find('.mainmenu-area .main-item.selected');
        return item.length > 0 ? item.eq(0) : null;
    };
    MainMenuClass.prototype.hideSubmenu = function (menuItem) {
        this.jq.find(".submenu-area .submenu-items." + menuItem.attr('id')).addClass('hidden');
    };
    MainMenuClass.prototype.isSelected = function (menuItem) {
        return menuItem.hasClass('selected');
    };
    MainMenuClass.prototype.selectMenuItem = function (menuItem, event) {
        if (this.isSelected(menuItem)) {
            this.deselectMenuItem(menuItem, event);
        }
        else {
            var selectedItem = this.getSelectedMenuItem();
            if (selectedItem !== null) {
                this.deselectMenuItem(selectedItem, event);
            }
        }
        menuItem.addClass('selected');
        this.showSubmenu(menuItem);
        this.trigger('selectMainMenuItem', event, { menuItem: menuItem });
    };
    MainMenuClass.prototype.selectSubmenuItem = function (menuItem, event) {
        if (menuItem.attr('data-action')) {
            App.request().get({ url: "/" + menuItem.attr('data-action') });
        }
    };
    MainMenuClass.prototype.showSubmenu = function (menuItem) {
        this.jq.find(".submenu-area .submenu-items." + menuItem.attr('id')).removeClass('hidden');
    };
    MainMenuClass.prototype.init = function (properties) {
        var menu = this;
        this.jq.find('.mainmenu-area .main-item').click(function (event) {
            menu.selectMenuItem($(this), event);
            event.stopPropagation();
        });
        this.jq.find('.submenu-area .submenu-item').click(function (event) {
            menu.selectSubmenuItem($(this), event);
        });
        $('html').on('click', function (event) {
            var item = menu.getSelectedMenuItem();
            if (item !== null) {
                menu.deselectMenuItem(item, event);
            }
        });
        _super.prototype.init.call(this, properties);
    };
    return MainMenuClass;
}(ObjectClass));
//# sourceMappingURL=mainMenu.js.map