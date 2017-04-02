abstract class ObjectClass {
    public jq: JQuery;
    public properties: any = {
        onInit: []
    };

    constructor (properties: any = {}, jq?: JQuery) {
        this.props(properties);
        this.jq = jq;
        this.init(properties);
    }

    public afterChangeContent(bindName: string, bind: any, target: any): void {
        this.trigger('afterChangeContent', null, {bindName: bindName, bind: bind, target: target});
    }

    public beforeChangeContent(bindName: string, bind: any): void {
        this.trigger('beforeChangeContent', null, {bindName: bindName, bind: bind});
    }

    public changeContent(binds: any): void {
        let bindName: string;
        let bind: any;
        let dataBindElement: any;
        for(bindName in binds) {
            bind = binds[bindName];
            this.beforeChangeContent(bindName, bind);
            if (this.jq.attr('data-bind') === bindName) {
                dataBindElement = this.jq;
            } else {
                dataBindElement = this.jq.find(`[data-bind="${bindName}"]`);
                if (dataBindElement.length == 0) {
                    return;
                }
            }
            if (bind.options.append) {
                dataBindElement.append(bind.content);
            } else if (bind.options.prepend) {
                dataBindElement.prepend(bind.content);
            } else {
                dataBindElement.html(bind.content);
            }
            this.afterChangeContent(bindName, bind, dataBindElement);
        }
    }

    public init(properties: any = {}): void {
        this.trigger('init', null, properties);
    }

    public off(eventName: string, eventHandler?: (eventName: string, ...args: any[]) => void): void {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName]) {
            if (!eventHandler) {
                this.properties[eventName] = [];
            } else {
                let i: any;
                let h: (eventName: string, ...args: any[]) => void;
                for(i in this.properties[eventName]) {
                    if (this.properties[eventName] === eventHandler) {
                        delete this.properties[eventName][i];
                        break;
                    }
                }
            }
        }
    }

    public on(eventName: string, eventHandler: (eventName: string, ...args: any[]) => void): void {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName]) {
            this.properties[eventName].push(eventHandler);
        } else {
            this.properties[eventName] = [eventHandler];
        }
    }

    protected prepareEventName(eventName: string): string {
        if (!eventName.match('^on[A-Z]')) {
            eventName = 'on' + eventName.charAt(0).toUpperCase() + eventName.substr(1);
        }
        return eventName;
    }

    /**
     * Установка/получение свойств объекта
     *
     * @param string|object|null name
     * @param any value
     * @returns {any}
     * @since 2.0.0
     * @version 2.0.0
     */
    public props(name: any, value?: any): any {
        let result: any = null;
        if (name !== null) {
            console.log(typeof name);
            console.log(name);
            if (typeof name === "object") {
                let nameProp: string;
                let valueProps: any;
                for(nameProp in name) {
                    this.props(nameProp, name[nameProp]);
                }
            } else if (typeof name === "string") {
                if (value === null) {
                    result = this.properties[name];
                } else {
                    if (name.match('^on[A-Z]')) {
                        if (typeof value === "function") {
                            this.on(name, value);
                        } else {
                            let handler: (eventName: string, ...args: any[]) => void;
                            for(handler of value) {
                                this.props(name, handler);
                            }
                        }
                    } else {
                        this.properties.name = value;
                    }
                }
            }
        } else {
            result = this.properties;
        }
        return result;
    }

    public trigger(eventName: string, ...args: any[]): void {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName]) {
            let i: any;
            for(i in this.properties[eventName]) {
                this.properties[eventName][i](this, ...args);
            }
        }
    }
}
