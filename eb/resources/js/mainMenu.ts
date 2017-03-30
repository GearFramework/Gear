class MainMenuClass extends ObjectClass {
    /* Private */
    /* Protected */
    /* Public */

    public deselectMenuItem(menuItem: any, event: any): void {
        menuItem.removeClass('selected');
        this.hideSubmenu(menuItem);
        this.trigger('deselectMainMenuItem', event, {menuItem: menuItem});
    }

    public getSelectedMenuItem(): any {
        let item: any = this.jq.find('.mainmenu-area .main-item.selected');
        return item.length > 0 ? item.eq(0) : null;
    }

    public hideSubmenu(menuItem: any): void {
        this.jq.find(`.submenu-area .submenu-items.${menuItem.attr('id')}`).addClass('hidden');
    }

    public isSelected(menuItem: any): boolean {
        return menuItem.hasClass('selected');
    }

    public selectMenuItem(menuItem: JQuery, event: any): void {
        if (this.isSelected(menuItem)) {
            this.deselectMenuItem(menuItem, event);
        } else {
            let selectedItem: JQuery = this.getSelectedMenuItem();
            if (selectedItem !== null) {
                this.deselectMenuItem(selectedItem, event);
            }
        }
        menuItem.addClass('selected');
        this.showSubmenu(menuItem);
        this.trigger('selectMainMenuItem', event, {menuItem: menuItem});
    }

    public selectSubmenuItem(menuItem: any, event: any): void {
        if (menuItem.attr('data-action')) {
            App.request().get({url: `/${menuItem.attr('data-action')}`});
        }
    }

    public showSubmenu(menuItem: any): void {
        this.jq.find(`.submenu-area .submenu-items.${menuItem.attr('id')}`).removeClass('hidden');
    }

    /**
     * Инициализация меню
     *
     * @param properties
     * @since 0.0.1
     * @version 0.0.1
     */
    public init(properties: any): void {
        let menu: MainMenuClass = this;
        this.jq.find('.mainmenu-area .main-item').click(function(event: any) {
            menu.selectMenuItem($(this), event);
            event.stopPropagation();
        });
        this.jq.find('.submenu-area .submenu-item').click(function(event: any) {
            menu.selectSubmenuItem($(this), event);
        });
        $('html').on('click', function(event) {
            let item: any = menu.getSelectedMenuItem();
            if (item !== null) {
                menu.deselectMenuItem(item, event);
            }
        });
        super.init(properties);
    }
}