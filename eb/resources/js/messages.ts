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

    protected prepareEventName(eventName: string): string {
        return super.prepareEventName(eventName);
    }

    public showMessage(message: string): void {

    }

    public showNotify(message: string): void {

    }
}

$(document).ready(function () {
    AppClass.prototype.messages = (properties: any, jq?: any) => new MessagesClass(properties, jq);
});
