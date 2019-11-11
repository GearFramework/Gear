abstract class ObjectClass implements AnyObjectInterface {
    /* Private */
    /* Protected */
    protected _properties: ObjectPropertiesInterface;
    protected _jq: JQuery;
    /* Public */

    get jq(): JQuery {
        return this._jq;
    }

    /**
     * Получение свойств объекта
     *
     * @returns {ObjectPropertiesInterface}
     * @since 0.0.1
     * @version 0.0.1
     */
    get properties(): ObjectPropertiesInterface {
        return this._properties;
    }

    set jq(jq: JQuery) {
        this._jq = jq
    }

    /**
     * Установка свойств объекта
     *
     * @param {ObjectPropertiesInterface} props
     * @returns void
     * @since 0.0.1
     * @version 0.0.1
     */
    set properties(props: ObjectPropertiesInterface) {
        this._properties = props;
    }

    /**
     * Конструктор объекта
     *
     * @param {ObjectPropertiesInterface} properties
     * @param {JQuery|undefined} jq
     * @return {ObjectClass}
     * @since 0.0.1
     * @version 0.0.1
     */
    constructor (properties: ObjectPropertiesInterface, jq?: JQuery) {
        this.jq = jq;
        this.initDefaultProperties();
        this.properties = this.mergeProperties(properties);
        this.init();
    }

    /**
     * Инициализация объекта
     *
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public init(): void {};

    /**
     * Инициализация значений по-умолчанию свойств объекта
     *
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public initDefaultProperties(): void {
        this.properties = {
            onInit: [],
            onConstruct: [],
        };
    }

    /**
     * Сливает текущие свойства объекта с указанными и возвращает полученный результат
     *
     * @param {ObjectPropertiesInterface} constructProperties
     * @returns {ObjectPropertiesInterface}
     * @since 0.0.1
     * @version 0.0.1
     */
    public mergeProperties(constructProperties: ObjectPropertiesInterface): ObjectPropertiesInterface {
        let objectProperties: any = this.properties;
        let name: string = '';
        let value: any = undefined;
        for(name in constructProperties) {
            value = constructProperties[name];
            if (name.match(/^on[A-Z]/)) {
                if (typeof value === 'function') {
                    value = [value];
                }
                if (objectProperties[name] == undefined) {
                    objectProperties[name] = value;
                } else {
                    let i: any;
                    for(i in value) {
                        objectProperties[name].push(value[i]);
                    }
                }
            } else {
                objectProperties[name] = value;
            }
        }
        return objectProperties;
    }

    /**
     * Удаление указанного события или отдельного обработчика события
     *
     * @param {string} eventName
     * @param {EventHandlerInterface|undefined} eventHandler
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public off(eventName: string, eventHandler?: EventHandlerInterface): void {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName]) {
            if (!eventHandler) {
                this.properties[eventName] = [];
            } else {
                for (let i in this.properties[eventName]) {
                    if (this.properties[eventName] === eventHandler) {
                        delete this.properties[eventName][i];
                        break;
                    }
                }
            }
        }
    }

    /**
     * Установка обработчика указанного события
     *
     * @param {string} eventName
     * @param {EventHandlerInterface} eventHandler
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public on(eventName: string, eventHandler: EventHandlerInterface): void {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName]) {
            this.properties[eventName].push(eventHandler);
        } else {
            this.properties[eventName] = [eventHandler];
        }
    }

    /**
     * Подготовка названия события, например, из переданного 'click' делает 'onClick'
     *
     * @param {string} eventName
     * @return {string}
     * @since 0.0.1
     * @version 0.0.1
     */
    protected prepareEventName(eventName: string): string {
        if (!eventName.match(/^on[A-Z]/)) {
            eventName = 'on' + eventName.charAt(0).toUpperCase() + eventName.substr(1);
        }
        return eventName;
    }

    /**
     * Установка/получение свойств объекта
     *
     * @param {ObjectPropertiesInterface|string|undefined} name
     * @param {any} value
     * @returns {any}
     * @since 0.0.1
     * @version 0.0.1
     */
    public props(name?: ObjectPropertiesInterface|string, value?: any): any {
        let result: any = null;
        if (name !== undefined) {
            if (typeof name === "object") {
                let nameProp: string;
                let valueProps: any;
                for(nameProp in name) {
                    this.props(nameProp, name[nameProp]);
                }
            } else if (typeof name === "string") {
                if (value === undefined) {
                    result = this.properties[name];
                } else {
                    if (name.match('^on[A-Z]')) {
                        if (typeof value === "function") {
                            this.on(name, value);
                        } else {
                            let handler: EventHandlerInterface;
                            for(handler of value) {
                                this.props(name, handler);
                            }
                        }
                    } else {
                        this.properties[name] = value;
                    }
                }
            }
        } else {
            result = this.properties;
        }
        return result;
    }

    /**
     * Генерация указанного события
     *
     * @param {string} eventName
     * @param {any|undefined} event
     * @param {EventParamsInterface|undefined} params
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public trigger(eventName: string, event?: any, params?: EventParamsInterface): boolean {
        let result : boolean = true;
        let lastResult : boolean = result;
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName] !== undefined) {
            let i: any;
            for(i in this.properties[eventName]) {
                result = this.properties[eventName][i](this, event, params);
                if (lastResult === true && result !== undefined && result !== null) {
                    lastResult = result;
                }
            }
        }
        return lastResult;
    }
}
