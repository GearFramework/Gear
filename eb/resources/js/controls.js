function ToolbarClass() {
    /* Устанавливаем свойства по-умолчанию */
    this.properties = {
        onInit: []
    };
    /* вызываем родительский конструктор */
    return ObjectClass.apply(this, arguments);
}

/* Наследуемся от ObjectClass */
ToolbarClass.prototype = Object.create(ObjectClass.prototype);
ToolbarClass.prototype.constructor = ToolbarClass;

/* Переопределяем унаследованное от ObjectClass событие ToolbarClass.onInit */
ToolbarClass.prototype.onInit = function(event) {
    ObjectClass.prototype.onInit.apply(this, arguments);
};

function ButtonClass() {
    /* Устанавливаем свойства по-умолчанию */
    this.properties = {
        action: function() {},
        onInit: []
    };
    /* вызываем родительский конструктор */
    return ObjectClass.apply(this, arguments);
}

/* Наследуемся от ObjectClass */
ButtonClass.prototype = Object.create(ObjectClass.prototype);
ButtonClass.prototype.constructor = ButtonClass;

ButtonClass.prototype.click = function() {
    var button = this;
    this.props('action')();
};

/* Переопределяем унаследованное от ObjectClass событие ButtonClass.onInit */
ButtonClass.prototype.onInit = function(event) {
    var button = this;
    this.jq.on('click', function(event) {
        button.click();
    });
    ObjectClass.prototype.onInit.apply(this, arguments);
};

