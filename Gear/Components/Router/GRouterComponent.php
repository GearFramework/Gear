<?php

namespace Gear\Components\Router;

use Gear\Core;
use Gear\Interfaces\IController;
use Gear\Interfaces\IRequest;
use Gear\Interfaces\IResponse;
use Gear\Library\GComponent;
use Gear\Traits\TFactory;

/**
 * Роутер
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GRouterComponent extends GComponent
{
    /* Traits */
    use TFactory;
    /* Const */
    /* Private */
    private $_currentController = null;
    private $_defaultController = 'index';
    private $_defaultControllersPath = 'Controllers';
    private $_request = null;
    private $_response = null;
    private $_routes = [];
    /* Protected */
    protected static $_isInitialized = false;
    /* Public */

    /**
     * Получение роута и запуск соответствующего контроллера
     *
     * @param IRequest $request
     * @param IResponse $response
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exec(IRequest $request, IResponse $response)
    {
        $this->request = $request;
        $this->response = $response;
        $controller = $this->getController();
        return $controller->run($this->request);
    }

    /**
     * Возвращает название класса контроллера по указанному пути
     *
     * @param string $route
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getClassByRoute(string $route): string
    {
        $class = $this->getClassByRouteInRoutes($route);
        if (empty($class)) {
            foreach (explode('/', $route) as $item) {
                if ($item === 'a') {
                    break;
                } elseif ($item === '') {
                    $class .= '/';
                } else {
                    $class = ucfirst($item) . '/';
                }
            }
            $class = Core::resolvePath(preg_replace('#/$#', '', $class));
            if (empty($class)) {
                $class = Core::resolvePath($this->defaultControllersPath . '/' . ucfirst($this->defaultController) . 'Controller');
            }
        }
        return $class;
    }

    /**
     * Возвращает название класса контроллера по указанному пути в списке путей
     *
     * @param string $route
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getClassByRouteInRoutes(string $route): string
    {
        $routes = $this->routes;
        $class = '';
        if (isset($routes[$route])) {
            $class = $routes[$route];
        } else {
            $items = explode('/', $route);
            array_pop($items);
            $counter = count($items);
            while ($counter > 0) {
                $path = implode('/', $items);
                if (isset($routes[$path])) {
                    $class = $routes[$path];
                    break;
                }
                array_pop($items);
                -- $counter;
            }
        }
        return $class;
    }

    /**
     * Возвращает текущий контроллер, если таковой определен
     *
     * @return IController
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getController(): IController
    {
        if ($this->_currentController === null) {
            $route = $this->getRequestRoute();
            $class = $this->getClassByRoute($route);
            if (empty($class)) {
                throw self::ControllerClassIsEmptyException(['route' => $route]);
            }
            $nameRoute = substr($route, strrpos('/', $route));
            $this->_currentController = $this->factory([
                'class' => $class,
                'name' => $nameRoute,
                'route' => $route,
            ]);
        }
        return $this->_currentController;
    }

    /**
     * Возвращает название контроллера, запускаемого по-умолчанию
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultController(): string
    {
        return $this->_defaultController;
    }

    /**
     * Возвращает путь к папке с контроллерами
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultControllersPath(): string
    {
        return $this->_defaultControllersPath;
    }

    /**
     * Возвращает инстанс запроса
     *
     * @return IRequest
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRequest(): IRequest
    {
        return $this->_request;
    }

    /**
     * Возвращает маршрут из запроса пользователя
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRequestRoute(): string
    {
        $route = $this->request->param('r', null);
        if (!$route) {
            $route = $this->defaultController;
        }
        return $route;
    }

    /**
     * Возвращает инстанс ответа
     *
     * @return IResponse
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getResponse(): IResponse
    {
        return $this->_response;
    }

    /**
     * Возвращает список роутов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRoutes(): array
    {
        return $this->_routes;
    }

    /**
     * Устанавливает название контроллера, запускаемого по-умолчанию
     *
     * @param string $defaultController
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDefaultController(string $defaultController)
    {
        $this->_defaultController = $defaultController;
    }

    /**
     * Устанавливает путь к папке с контроллерами
     *
     * @param string $defaultControllersPath
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDefaultControllersPath(string $defaultControllersPath)
    {
        $this->_defaultControllersPath = $defaultControllersPath;
    }

    /**
     * Устанавливает инстанс запроса
     *
     * @param IRequest $request
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setRequest(IRequest $request)
    {
        $this->_request = $request;
    }

    /**
     * Устанавливает инстанс ответа
     *
     * @param IResponse $response
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setResponse(IResponse $response)
    {
        $this->_response = $response;
    }

    /**
     * Устанавливает список роутов
     *
     * @param \Closure|array $routes
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setRoutes($routes)
    {
        if ($routes instanceof \Closure) {
            $routes = $routes($this);
        }
        if (!is_array($routes)) {
            throw self::InvalidRoutesException();
        }
        $this->_routes = $routes;
    }
}
