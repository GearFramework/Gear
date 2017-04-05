/**
 * Базовый класс объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class ObjectClass {
    /* Private */
    /* Protected */
    /* Public */
    public jq: JQuery;
    public properties: any = {
        onInit: []
    };

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
        this.props(properties);
        this.jq = jq;
        this.init(properties);
    }

    /**
     * Генерирует событие onAfterCangeContent, которое должно возникать после того
     * как у target был изменён контент
     *
     * @param string bindName
     * @param any bind
     * @param JQuery target
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public afterChangeContent(bindName: string, bind: any, target: JQuery): void {
        this.trigger('afterChangeContent', null, {bindName: bindName, bind: bind, target: target});
    }

    /**
     * Генерирует событие onBeforeCangeContent, которое должно возникать до того
     * как у target быдет изменён контент
     *
     * @param string bindName
     * @param any bind
     * @param JQuery target
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public beforeChangeContent(bindName: string, bind: any, target: JQuery): void {
        this.trigger('beforeChangeContent', null, {bindName: bindName, bind: bind, target: target});
    }

    /**
     * Изменение контента внтури объекта, согласно биндингам.
     * Вызывается после успешного запроса к серверу. Должен вызываться из подписанного объектом обработчика события
     * AppClass.onChangeContent
     *
     * @param object data
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public changeContent(data: any): void {
        let bindName: string;
        let bind: any;
        let dataBindElement: any;
        for(bindName in data.binds) {
            bind = data.binds[bindName];
            if (this.jq.attr('data-bind') === bindName) {
                dataBindElement = this.jq;
            } else {
                dataBindElement = this.jq.find(`[data-bind="${bindName}"]`);
                if (dataBindElement.length == 0) {
                    return;
                }
            }
            this.beforeChangeContent(bindName, bind, dataBindElement);
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

    /**
     * Инициализация объекта
     *
     * @param Object properties
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public init(properties: Object = {}): void {
        this.trigger('init', null, properties);
    }

    /**
     * Удаление указанного события или отдельного обработчика события
     *
     * @param string eventName
     * @param function|null eventHandler
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public off(eventName: string, eventHandler?: EventHandler): void {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName]) {
            if (!eventHandler) {
                this.properties[eventName] = [];
            } else {
                let i: any;
                let h: EventHandler;
                for(i in this.properties[eventName]) {
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
     * @param string eventName
     * @param function|null eventHandler
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public on(eventName: string, eventHandler: EventHandler): void {
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
     * @param string eventName
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
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
     * @returns any
     * @since 0.0.1
     * @version 0.0.1
     */
    public props(name?: any, value?: any): any {
        let result: any = null;
        if (name !== null) {
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
                            let handler: EventHandler;
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

    /**
     * Генерация указанного события
     *
     * @param string eventName
     * @param any event
     * @param object params
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public trigger(eventName: string, event?: any, params: any = {}): void {
        eventName = this.prepareEventName(eventName);
        if (this.properties[eventName] !== undefined) {
            let i: any;
            for(i in this.properties[eventName]) {
                this.properties[eventName][i](this, event, params);
            }
        }
    }
}
