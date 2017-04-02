class MainMenuClass extends ObjectClass {
    /* Private */
    /* Protected */
    /* Public */

    public deselectMenuItem(menuItem: JQuery, event: Event): void {
        menuItem.removeClass('selected');
        this.hideSubmenu(menuItem);
        this.trigger('deselectMainMenuItem', event, {menuItem: menuItem});
    }

    public getSelectedMenuItem(): JQuery|null {
        let item: any = this.jq.find('.mainmenu-area .main-item.selected');
        return item.length > 0 ? item.eq(0) : null;
    }

    public hideSubmenu(menuItem: JQuery): void {
        this.jq.find(`.submenu-area .submenu-items.${menuItem.attr('id')}`).addClass('hidden');
    }

    public isSelected(menuItem: JQuery): boolean {
        return menuItem.hasClass('selected');
    }

    public selectMenuItem(menuItem: JQuery, event: Event): void {
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

    public selectSubmenuItem(menuItem: JQuery, event: Event): void {
        if (menuItem.attr('data-action')) {
            App.request().get({url: `/${menuItem.attr('data-action')}`});
        }
    }

    public showSubmenu(menuItem: JQuery): void {
        this.jq.find(`.submenu-area .submenu-items.${menuItem.attr('id')}`).removeClass('hidden');
    }

    /**
     * Инициализация меню
     *
     * @param properties
     * @since 0.0.1
     * @version 0.0.1
     */
    public init(properties: Object): void {
        let menu: MainMenuClass = this;
        this.jq.find('.mainmenu-area .main-item').click(function(event: Event): void {
            menu.selectMenuItem($(this), event);
            event.stopPropagation();
        });
        this.jq.find('.submenu-area .submenu-item').click(function(event: Event): void {
            menu.selectSubmenuItem($(this), event);
        });
        $('html').on('click', function(event: Event): void {
            let item: JQuery = menu.getSelectedMenuItem();
            if (item !== null) {
                menu.deselectMenuItem(item, event);
            }
        });
        super.init(properties);
    }
}