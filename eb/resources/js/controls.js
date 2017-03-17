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
        onInit: []
    };
    /* вызываем родительский конструктор */
    return ObjectClass.apply(this, arguments);
}

/* Наследуемся от ObjectClass */
ButtonClass.prototype = Object.create(ObjectClass.prototype);
ButtonClass.prototype.constructor = ButtonClass;

/* Переопределяем унаследованное от ObjectClass событие ButtonClass.onInit */
ButtonClass.prototype.onInit = function(event) {
    ObjectClass.prototype.onInit.apply(this, arguments);
};

