function AppClass(properties) {
    /* Устанавливаем свойства по-умолчанию */
    this.properties = {
        onInit: [],
        onResize: [],
        onChangeContent: [],
        controllers: {
            auth: ''
        },
        errorsHandlers: {
            401: function() {
                self.location = '/' + App.props('controllers').auth;
            },
            403: function() {
                self.location = '/' + App.props('controllers').denied;
            }
        }
    };
    /* вызываем родительский конструктор */
    return ObjectClass.apply(this, arguments);
}

/* Наследуемся от ObjectClass */
AppClass.prototype = Object.create(ObjectClass.prototype);
AppClass.prototype.constructor = AppClass;

/**
 * Изменение контента на странице
 *
 * @param binds
 */
AppClass.prototype.changeContent = function(binds) {
    for(var bindName in binds) {
        var content = binds[bindName];
        if (this.isPossibleBind(bindName)) {
            this.onBeforeChangeContent(bindName, content);
            this.setContent(bindName, content);
            this.onAfterChangeContent(bindName, content);
        }
    }
};

AppClass.prototype.errorResponse = function(xhr) {
    if (this.properties.errorsHandlers[xhr.status] !== undefined) {
        this.properties.errorsHandlers[xhr.status]();
    }
};

/* Переопределяем унаследованное от ObjectClass событие AppClass.onInit */
AppClass.prototype.onInit = function(event) {
    var application = this;
    $(window).on('resize', function(event) { application.onResize(event); });
    ObjectClass.prototype.onInit.apply(this, arguments);
};

/**
 * Событие измененя контента на странице, подписавшиеся компоненты при получении своего контента могут его изменять
 *
 * @param bindName
 * @param content
 */
AppClass.prototype.onChangeContent = function(bindName, content) {
    this.jq.find('.top-fix').height(this.jq.find('.top').height());путинввввававав
    this.trigger('changeContent', undefined, {bindName: bindName, content: content});
};

/* Добавляем событие AppClass.onResize */
AppClass.prototype.onResize = function(event) {
    this.trigger('resize', event);
};

var App = undefined;
