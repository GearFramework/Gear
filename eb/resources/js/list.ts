/**
 * Класс для работы со списками
 *
 * @since 0.0.1
 * @version 0.0.1
 */
class ListClass extends ObjectClass {
    /* Private */
    /* Protected */
    protected _defaultProperties: any = {
        messenger: console,
        progress: null,
        /* Селектор элементов, которые участвуют в списке */
        selector: '.item',
        /* Область по которой должен производится клик мышкой для выбора элемента, по-умолчанию весь элемент */
        areaItemClickSelector : null,
        /* Класс который определяет выбранность элемента */
        selectedClass : 'selected',
        /* Возможность выбора нескольких элементов при помощт crtl или shift-клавиш */
        multiSelect : true,
        clearSelectionsClickOut : false,
        ctrlMultiSelect : true,
        disableHtmlSelecting : true,
        onInit: []
    };
    protected _items: any = null;
    /* Public */

    /**
     * Конструктор объекта
     *
     * @param Object properties
     * @param JQuery jq
     * @return ObjectClass
     * @since 0.0.1
     * @version 0.0.1
     */
    constructor (properties: Object = {}, jq?: JQuery) {
        super(properties, jq);
        this.props(this._mergeProperties(this._propertiesDefault, properties));
        this.init(properties);
    }

    public get items(): any {
        return this._items;
    }

    public set items(items: any) {
        this._items = items;
    }

    /**
     * Обработчик события, возникающего при клике на элементе списка
     *
     * @param any item
     * @param any event
     * @return void
     * @since 2.0.0
     * @version 2.0.0
     */
    public clickItem(item: any, event: any): void {
        if (this.properties.multiSelect && event.shiftKey == true) {
            let itemsAllow: any = this.items;
            var first = this.getSelectedItems()[0];
            var last = item;
            if (first.length == 0)
                this.selectItem(last);
            else {
                var indexFirst = itemsAllow.index(first);
                var indexLast = itemsAllow.index(last);
                if (indexFirst < indexLast) {
                    for(var index = indexFirst + 1; index <= indexLast; index ++)
                        this.selectItem(itemsAllow.eq(index));
                } else {
                    for(var index = indexLast; index < indexFirst; index ++)
                        this.selectItem(itemsAllow.eq(index));
                }
            }
        } else if ((this.properties.multiSelect && this.properties.ctrlMultiSelect && (event.ctrlKey == true || event.metaKey == true)) ||
                   (this.properties.multiSelect && !this.properties.ctrlMultiSelect && event.ctrlKey == false && event.metaKey == false)) {
            this.toggleItem(item);
        } else {
            if (this.countSelected() > 1)
            {
                this.deselectItem(this.getSelectedItems());
                this.selectItem(item);
            } else {
                if (!this.hasSelected(item)) {
                    this.deselectItem(this.getSelectedItems());
                    this.selectItem(item);
                } else {
                    this.deselectItem(item);
                }
            }
        }
        this.trigger('clickItem', event, {item: item, event: event});
    }

    /**
     * Возвращает количество выделенных элементов
     *
     * @return {number}
     * @since 2.0.0
     * @version 2.0.0
     */
    public countSelected(): number {
        return this.getSelectedItems().length;
    }

    /**
     * Снимает выделение с элемента
     *
     * @param any item
     * @return void
     * @since 2.0.0
     * @version 2.0.0
     */
    public deselectItem(item: any): void {
        item.removeClass(this.props('selectedClass'));
        this.trigger('deselectItem', null, {item: item});
    }

    /**
     * Возвращает массив выделенных элементов
     *
     * @return {any[]}
     * @since 2.0.0
     * @version 2.0.0
     */
    public getSelectedItems(): any[] {
        let items: any[];
        this.items.each(function(key: string, value: any) {
            if (value.hasClass(this.props('selectedClass'))) {
                items.push($(value));
            }
        });
        return items;
    }

    /**
     * Возвращает true, если указанный элемент выделен, иначе - false
     *
     * @param item
     * @return {boolean}
     * @since 2.0.0
     * @version 2.0.0
     */
    public hasSelected(item: any): boolean {
        return item.hasClass(this.props('selectedClass'));
    }

    /**
     * Инициализация списка
     *
     * @param any properties
     * @return void
     * @since 2.0.0
     * @version 2.0.0
     */
    public init(properties: any): void {
        this.loadItems();
        super.init(properties);
    }

    /**
     * Загрузка элементов списка
     *
     * @return void
     * @since 2.0.0
     * @version 2.0.0
     */
    public loadItems(): void {
        let list: ListClass = this;
        this.items = $(this.props('selector'));
        let workItems: any = null;
        if (this.properties.areaItemClickSelector === null) {
            workItems = this.items;
        } else {
            workItems = this.items.find(this.properties.areaItemClickSelector);
        }
        workItems.on('click', function(event: any): void { list.clickItem($(this), event); });
    }

    /**
     * Перезагрузка элементов
     *
     * @return void
     * @since 2.0.0
     * @version 2.0.0
     */
    public reloadItems(): void {
        let workItems: any = null;
        if (this.properties.areaItemClickSelector === null) {
            workItems = this.items;
        } else {
            workItems = this.items.find(this.properties.areaItemClickSelector);
        }
        workItems.off('click');
        this.items = null;
        this.loadItems();
    }

    /**
     * Выделение элемента списка
     *
     * @param any item
     * @return void
     * @since 2.0.0
     * @version 2.0.0
     */
    public selectItem(item: any): void {
        item.addClass(this.props('selectedClass'));
        this.trigger('selectItem', null, {item: item});
    }

    /**
     * Выделяет элемент если не выделен, иначе снимает выделение
     *
     * @param any item
     * @return void
     * @since 2.0.0
     * @version 2.0.0
     */
    public toggleItem(item: any): void {
        this.hasSelected(item) ? this.deselectItem(item) : this.selectItem(item);
    }
}
