<?php

namespace gear\components\controllers;

use gear\Core;
use gear\interfaces\IController;
use gear\interfaces\IRequest;
use gear\library\GComponent;
use gear\library\GController;
use gear\library\GEvent;

/**
 * Роутинг контроллеров
 * Можно использовать правила nginx rewrite
 *
 * if ($request_uri !~ ^/index\.php) {
 *     rewrite ^/([a-zA-Z0-9_/]*) /index.php?r=$1 last;
 * }
 *
 * Контроллер передаётся в параметре "r": index.php?r=index, index.php?r=resources/css
 * Для указания какой api-метод использовать, необходимо в параметер "r" указать /a/<название метода>:
 * index.php?r=resources/css/a/get
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GControllersComponent extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    /**
     * @var string $_defaultControllerName название контроллера исполняемого по-умолчанию
     */
    protected $_defaultControllerName = 'index';
    protected $_rewrite = false;
    /**
     * @var IController $_currentController инстанс текущего контроллера
     */
    protected $_currentController = null;
    /**
     * @var array $_mapControllers карта контроллеров
     * $_mapControllers => [
     *      'dir/controllers/ControllerName' => '/namespace/toControllers/ControllerClassName',
     *      'dir/controllers/ControllerName' => ['/namespace/toControllers/ControllerClassName', 'actionName'],
     *      'dir/controllers/*' => '/namespace/toControllers',
     * ]
     */
    protected $_mapControllers = [];
    /**
     * @var IRequest $_request пользовательский запрос
     */
    protected $_request = null;
    /* Public */

    /**
     * Получение и запуск нужного контроллера
     *
     * @param IRequest $request
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exec(IRequest $request)
    {
        $result = false;
        if ($this->beforeExecRouting($request)) {
            $this->request = $request;
            $controller = $this->currentController;
            /** @var \Closure|\gear\interfaces\IController $controller */
            $result = $controller($request);
        }
        return $result;
    }

    /**
     * Возвращает экземпляр текущего контроллера
     *
     * @return IController
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCurrentController(): IController
    {
        if (!($this->_currentController instanceof IController)) {
            $path = trim((string)$this->request->r);
            $path = $this->validate(null, $path, null, function($value) {
                return preg_replace('/[^a-zA-Z0-9_\/]/', '', $value);
            });
            $this->_currentController = $this->getRouteController($path);
        }
        return $this->_currentController;
    }

    /**
     * Возвращает название контроллера, исполняемого по-умолчанию
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultControllerName(): string
    {
        return $this->_defaultControllerName;
    }

    /**
     * Возвращает карту путей к контроллерам
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMapControllers(): array
    {
        return $this->_mapControllers;
    }

    /**
     * Возвращает экземпляр запроса
     *
     * @return IRequest
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRequest(): IRequest
    {
        if ($this->_request === null) {
            if (is_object($this->owner)) {
                $this->_request = $this->owner->request();
            } else {
                $this->_request = Core::app()->request();
            }
        }
        return $this->_request;
    }

    /**
     * Возвращает используется ли mod_rewrite или rewrite (nginx) true или false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRewrite(): bool
    {
        return $this->_rewrite;
    }

    /**
     * Возвращает контроллер согласно пути из запроса
     *
     * @param string $path
     * @return IController
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRouteController(string $path): IController
    {
        if (preg_match('#(^a/|/a/|/a$)#', $path)) {
            list($controllerPath, $actionName) = preg_split('#(^a/|/a/|/a$)#', $path);
        } else {
            $controllerPath = $path;
            $actionName = '';
        }
        if (($controllerName = $this->existsInMapControllers($controllerPath, true))) {
            $controllerClass = $this->_mapControllers[$controllerName];
            if (is_array($controllerClass)) {
                list($controllerClass, $actionName) = $controllerClass;
            }
            $properties = ['name' => $controllerName];
        } else {
            if (!($controllerName = basename($controllerPath))) {
                $controllerName = $this->defaultControllerName;
            }
            $dir = strpos($controllerPath, '/') !== false ? '/' . dirname($controllerPath) . '/' : '/';
            $dir = '/' . Core::app()->namespace . '/controllers' . $dir;
            $controllerClass = $dir . ucfirst($controllerName) . 'Controller';
            $properties = ['name' => $path ? $controllerPath : $controllerName];
        }
        $controllerClass = str_replace('/', '\\', $controllerClass);
        $controller = new $controllerClass($properties, $this);
        if ($actionName) {
            $controller->defaultApiName = $actionName;
        }
        return $controller;
    }

    /**
     * Возвращает true, если контроллер присутствует в карте, иначе false
     *
     * @param array|string $name
     * @param bool $returnPath
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function existsInMapControllers($name, bool $returnPath = false)
    {
        if (is_string($name)) {
            $name = explode('/', $name);
        }
        $found = false;
        $path = '';
        foreach($name as $part) {
            $path .= ($path ? '/' : '') . $part;
            if (isset($this->_mapControllers[$path])) {
                $found = $path;
                break;
            } else if (isset($this->_mapControllers[$path . '/*'])) {
                $found = $path . '/*';
                break;
            }
        }
        return $returnPath ? $found : ($found ? true : false);
    }

    /**
     * Установка текущего контроллера
     *
     * @param \Closure|array|IController $controller
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCurrentController($controller)
    {
        if ($controller instanceof \Closure) {
            $controller = new GController(['bindingClosure' => $controller], $this);
        } else if (is_array($controller)) {
            list($class,, $properties) = Core::configure($controller);
            if ($class) {
                $controller = new $class($properties, $this);
            }
        }
        if ($controller instanceof IController) {
            $this->_currentController = $controller;
        } else {
            $this->exceptionController('Invalid controller');
        }
    }

    /**
     * Устанавливает название контроллера, исполняемого по-умолчанию
     *
     * @param string $defaultControllerName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDefaultControllerName(string $defaultControllerName)
    {
        $this->_defaultControllerName = $defaultControllerName;
    }

    /**
     * Устанавливает карту путей к контроллерам
     *
     * @param array $mapControllers
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setMapControllers(array $mapControllers)
    {
        $this->_mapControllers = $mapControllers;
    }

    public function setRequest(IRequest $request)
    {
        $this->_request = $request;
    }

    /**
     * Устанавливает используется ли mod_rewrite (apache) или rewrite (nginx)
     *
     * @param bool $rewrite
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setRewrite(bool $rewrite)
    {
        $this->_rewrite = $rewrite;
    }

    public function beforeExecRouting($request)
    {
        return Core::trigger('onBeforeExecRouting', new GEvent($this, ['target' => $this, 'request' => $request]));
    }
}
