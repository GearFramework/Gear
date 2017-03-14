function MessageClass() {
    /* Устанавливаем свойства по-умолчанию */
    this.properties = {
        message: '',
        onInit: []
    };
    /* вызываем родительский конструктор */
    return ObjectClass.apply(this, arguments);
}

/* Наследуемся от ObjectClass */
MessageClass.prototype = Object.create(ObjectClass.prototype);
MessageClass.prototype.constructor = MainMenuClass;

/* Переопределяем унаследованное от ObjectClass событие AppClass.onInit */
MessageClass.prototype.onInit = function(event) {
    ObjectClass.prototype.onInit.apply(this, arguments);
};

$(document).ready(function() {
    AppClass.prototype.messages = function(properties) {
        return new MessageClass(properties);
    };
});
