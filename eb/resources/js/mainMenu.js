function MainMenuClass(properties) {
    /* Устанавливаем свойства по-умолчанию */
    this.properties = {
        onInit: [],
        onResize: [],
        onChangeContent: []
    };
    /* вызываем родительский конструктор */
    return ObjectClass.apply(this, arguments);
}

/* Наследуемся от ObjectClass */
MainMenuClass.prototype = Object.create(ObjectClass.prototype);
MainMenuClass.prototype.constructor = MainMenuClass;

/**
 * Изменение контента на странице
 *
 * @param binds
 */
MainMenuClass.prototype.changeContent = function(binds) {
    for(var bindName in binds) {
        this.onChangeContent(bindName, binds[bindName]);
    }
};

MainMenuClass.prototype.deselect = function(item) {
    item.removeClass('selected');
    this.hideSubmenu(item);
    this.onDeselectMainMenuItem(item);
};

MainMenuClass.prototype.select = function(item) {
    item.addClass('selected');
    this.showSubmenu(item);
    this.onSelectMainMenuItem(item);
};

MainMenuClass.prototype.hideSubmenu = function(item) {
    this.jq.find('.submenu-area .submenu-items.' + item.attr('id')).addClass('hidden');
};

MainMenuClass.prototype.showSubmenu = function(item) {
    this.jq.find('.submenu-area .submenu-items.' + item.attr('id')).removeClass('hidden');
};

MainMenuClass.prototype.selectMenuItem = function(item, event) {
    var selectedItem = this.getSelectedMenuItem();
    if (this.isSelected(item)) {
        this.deselect(item);
    } else {
        if (selectedItem !== undefined) {
            this.deselect(selectedItem);
        }
        this.select(item);
    }
};

MainMenuClass.prototype.isSelected = function(item) {
    return item.hasClass('selected');
};

MainMenuClass.prototype.getSelectedMenuItem = function() {
    var item = this.jq.find('.mainmenu-area .main-item.selected');
    return item.length > 0 ? item.eq(0) : undefined;
};

/* Переопределяем унаследованное от ObjectClass событие AppClass.onInit */
MainMenuClass.prototype.onInit = function(event) {
    var menu = this;
    this.jq.find('.mainmenu-area .main-item').click(function(event) { menu.selectMenuItem($(this), event) });
    ObjectClass.prototype.onInit.apply(this, arguments);
};

MainMenuClass.prototype.onSelectMainMenuItem = function(item) {
    this.trigger('selectMainMenuItem', undefined, {item: item});
};

MainMenuClass.prototype.onDeselectMainMenuItem = function(item) {
    this.trigger('deselectMainMenuItem', undefined, {item: item});
};

$(document).ready(function() {
    App.mainMenu = new MainMenuClass({}, $('#mainMenu'));
});
