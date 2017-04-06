class MessagesClass extends ObjectClass {
    /* Private */
    /* Protected */
    /* Public */
    public properties: Object = {
        notifyContainer: null,
        shadow: null,
        onShow: [],
        onClose: []
    };

    public showMessage(message: string): void {

    }

    public showNotify(message: string): void {

    }
}

$(document).ready(function () {
    AppClass.prototype.messages = (properties: any, jq?: any) => new MessagesClass(properties, jq);
});
