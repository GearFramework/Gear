class VendorsClass extends ObjectClass {
    /* Private */
    /* Protected */
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

    public init(properties: any): void {
        let vendors: VendorsClass = this;
        $('.wrapper>.page-wrapper').css('margin-left', this.jq.parent().width());
        this.jq.find('.vendor-tile').on('click', (event: Event): void => this.selectVendor($(event.currentTarget)));
        App.on('resize', (sender: any, event?: any, params?: any): void => {
            let height: number = App.getHeight() - this.jq.parent().offset().top;
            this.jq.parent().height(height);
            $('.wrapper>.page-wrapper').height(height);
        });
        super.init(properties);
    }

    public selectVendor(vendor: JQuery): void {
        App.request({url: vendor.attr('data-action')}).get();
        console.log(vendor.attr('data-action'));
    }
}

class VendorOrdersClass extends ObjectClass {
    /* Private */
    /* Protected */
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

    public init(properties: any): void {
        super.init(properties);
    }
}

class VendorCategoriesClass extends ObjectClass {
    /* Private */
    /* Protected */
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

    public init(properties: any): void {
        super.init(properties);
    }
}

App.vendors = new VendorsClass({
    navigator: new ToolbarClass({

    }, $('.vendors-navigator-panel')),
    toolbar: new ToolbarClass({
        buttons: {
            add: new ButtonClass({
                action: function(): void {
                    alert('add');
                }
            }, $('.vendors-toolbar-panel .button.add')),
            edit: new ButtonClass({
                action: function(): void {
                    alert('edit');
                }
            }, $('.vendors-toolbar-panel .button.edit'))
        }
    }, $('.vendors-toolbar-panel'))
}, $('.vendors-list'));
