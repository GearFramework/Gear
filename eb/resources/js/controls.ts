interface ControlsInterface {
    click(event: any): void;
}

class ControlsClass extends ObjectClass {
    /* Private */
    /* Protected */
    /* Public */
    public controls: any[] = null;

    public checkAppendControls(sender: any, event: any, params: any): void {

    }

    public init(properties: Object = {}): void {
        App.on('beforeChangeContent', this.checkAppendControls);
        this.loadControls();
        super.init(properties);
    }

    public loadControls(): void {
        this.controls = [];
        $('.controls.button, .controls.radio, .controls.checkbox').each(function(key: number, control: any): void {
            control = $(control);
            if (control.hasClass('button')) {
                this.controls.push(new ButtonClass({}, control));
            } else if (control.hasClass('checkbox')) {
                this.controls.push(new CheckButtonClass({}, control));
            } else if (control.hasClass('readio')) {
                this.controls.push(new RadioButtonClass({}, control));
            }
        });
    }
}

class ButtonClass extends ObjectClass implements ControlsInterface {
    /* Private */
    /* Protected */
    /* Public */
    public action: string;

    public click(event: any): void {

    }

    public init(properties: any): void {
        this.action = this.jq.attr('data-action');
        let control: ControlsInterface = this;
        this.jq.on('click', function(event: any): void {
            control.click(event);
        });
        super.init(properties);
    }
}

class CheckButtonClass extends ButtonClass {
    /* Private */
    /* Protected */
    /* Public */

    public init(properties: any): void {
        let action: string = this.jq.attr('data-action');
        super.init(properties);
    }
}

class RadioButtonClass extends ButtonClass {
    /* Private */
    /* Protected */
    /* Public */

    public init(properties: any): void {
        let action: string = this.jq.attr('data-action');
        super.init(properties);
    }
}

class ToolbarClass extends ObjectClass {
    /* Private */
    /* Protected */
    /* Public */

    public init(properties: any): void {
        super.init(properties);
    }
}

class ProgressBarClass extends ObjectClass {
    /* Private */
    /* Protected */
    protected _propertiesDefault: Object = {};
    protected _isStarted: boolean = false;
    protected _state: number = 0;
    protected _position: number = 0;
    /* Public */

    get isStarted(): boolean {
        return this._isStarted;
    }

    get state(): number {
        return this._state;
    }

    set position(position: number) {

    }

    set state(state: number) {
        this._state = state;
    }

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

    public inc(amount: number): void {
        if (this.state === 0) {
            this.start();
        } else if (this.state > 1) {
            return;
        } else {
            if (this.state >= 0 && this.state < 0.2) { amount = 0.1; }
            else if (this.state >= 0.2 && this.state < 0.5) { amount = 0.04; }
            else if (this.state >= 0.5 && this.state < 0.8) { amount = 0.02; }
            else if (this.state >= 0.8 && this.state < 0.99) { amount = 0.005; }
            else { amount = 0; }

            this.state += amount;
            if (this.state < 0)
                this.state = 0;
            if (this.state > 0.994)
                this.state = 0.994;
            this.position = this.state;
        }
    }

    public init(properties: any): void {
        super.init(properties);
    }

    public start(): void {
        if (!this.isStarted) {

        }
    }

    public stop(): void {
        if (this.isStarted) {

        }
    }
}
