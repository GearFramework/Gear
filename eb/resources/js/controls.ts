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
