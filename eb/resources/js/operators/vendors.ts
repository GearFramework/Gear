class VendorsClass extends ObjectClass {
    /* Private */
    /* Protected */
    /* Public */

    public init(properties: any): void {
        super.init(properties);
    }
}

AppClass.prototype.vendors = new VendorsClass({
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
