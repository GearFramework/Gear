function VendorsClass() {
    /* Устанавливаем свойства по-умолчанию */
    this.properties = {
        onInit: []
    };
    /* вызываем родительский конструктор */
    return ObjectClass.apply(this, arguments);
}

/* Наследуемся от ObjectClass */
VendorsClass.prototype = Object.create(ObjectClass.prototype);
VendorsClass.prototype.constructor = VendorsClass;

/* Переопределяем унаследованное от ObjectClass событие VendorsClass.onInit */
VendorsClass.prototype.onInit = function(event) {
    ObjectClass.prototype.onInit.apply(this, arguments);
};

App.vendors = new VendorsClass({
    navigator: new ToolbarClass({}, $('.vendors-navigator-panel')),
    toolbar: new ToolbarClass({
        buttons: {
            add: new ButtonClass({
                action: function() {
                    alert('add');
                }
            }, $('.vendors-toolbar-panel .button.add'))
        }
    }, $('.vendors-toolbar-panel'))
}, $('.vendors-list'));
