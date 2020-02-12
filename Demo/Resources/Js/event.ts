class GEvent implements EventInterface {
    /* Private */
    /* Protected */
    protected _sender: ObjectInterface|undefined;
    protected _target: ObjectInterface|undefined;
    protected _properties: EventParamsInterface|undefined;
    /* Public */

    get properties(): EventParamsInterface {
        return this._properties;
    }

    get sender(): ObjectInterface {
        return this._sender;
    }

    get target(): ObjectInterface {
        return this._target;
    }

    set properties(properties: EventParamsInterface) {
        this._properties = properties;
    }

    set sender(sender: ObjectInterface) {
        this._sender = sender;
    }

    set target(target: ObjectInterface) {
        this._target = target;
    }

    constructor (sender: ObjectInterface, target: ObjectInterface = undefined, properties: EventParamsInterface = undefined) {
        this.sender = sender;
        this.target = target !== undefined ? target : sender;
        this.properties = properties;
    }
}
