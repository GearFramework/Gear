<?php

namespace Gear\Components\Router;

use Gear\Core;
use Gear\Interfaces\ControllerInterface;
use Gear\Interfaces\FactoryInterface;
use Gear\Interfaces\RequestInterface;
use Gear\Interfaces\ResponseInterface;
use Gear\Library\GComponent;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Роутер
 *
 * @package Gear Framework
 *
 * @property null|ControllerInterface controller
 * @property null|ControllerInterface currentController
 * @property string defaultController
 * @property string defaultControllersPath
 * @property RequestInterface request
 * @property ResponseInterface response
 * @property array routes
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GRouterComponent extends GComponent implements FactoryInterface
{
    /* Traits */
    use FactoryTrait;
    /* Const */
    /* Private */
    private $_currentController = null;
    private $_defaultController = 'index';
    private $_defaultControllersPath = 'Controllers';
    private $_factoryProperties = [];
    private $_request = null;
    private $_response = null;
    private $_routes = [];
    /* Protected */
    /* Public */

    /**
     * Добавляет соответствие пути к классу контроллера
     *
     * @param string $path
     * @param string|array $class
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function addRoute(string $path, $class)
    {
        $this->_routes[$path] = $class;
    }

    /**
     * Добавляет массив соответствий путей к соответствующим классам контроллеров
     *
     * @param iterable $routes
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function addRoutes(iterable $routes)
    {
        foreach ($routes as $pathRoute => $class) {
            $this->addRoute($pathRoute, $class);
        }
    }

    /**
     * Получение роута и запуск соответствующего контроллера
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function exec(RequestInterface $request, ResponseInterface $response)
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
            $classPath = [];
            foreach (explode('/', $route) as $item) {
                if ($item === 'a') {
                    break;
                } elseif ($item === '') {
                    $classPath[] = '';
                } else {
                    $classPath[] = ucfirst($item);
                }
            }
            $class = implode('\\', $classPath);
            if (empty($class)) {
                $class = '\\' . Core::app()->namespace . '\\' . $this->defaultControllersPath . '/' . ucfirst($this->defaultController);
            } else {
                if ($class[0] !== '\\') {
                    $class = '\\' . Core::app()->namespace . '\\' . $this->defaultControllersPath . '\\' . $class;
                }
                //$class = Core::resolvePath(preg_replace('#/$#', '', $class));
            }
        }
        return $class;
    }

    /**
     * Возвращает название класса контроллера по указанному пути в списке путей
     *
     * @param string $route
     * @return string
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
     * @return ControllerInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getController(): ControllerInterface
    {
        if ($this->currentController === null) {
            $route = $this->getRequestRoute();
            $class = $this->getClassByRoute($route);
            if (empty($class)) {
                throw self::ControllerClassIsEmptyException(['route' => $route]);
            }
            $nameRoute = substr($route, strrpos('/', $route));
            $controller = [
                'class' => $class,
                'name' => $nameRoute,
                'route' => $route,
            ];
            $this->currentController = $this->factory($controller, $this);
        }
        return $this->currentController;
    }

    /**
     * Возвращает инстантс или NULL текушего контроллера
     *
     * @return ControllerInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCurrentController(): ?ControllerInterface
    {
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
     * @return RequestInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getRequest(): RequestInterface
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
     * @return ResponseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getResponse(): ResponseInterface
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

    public function redirect($path, $params = [])
    {
        if (is_array($params) && $params) {
            $p = [];
            foreach($params as $name => $value) {
                $p[] = "$name=$value";
            }
            $path .= '?' . implode('&', $p);
        } else if (is_string($params) && trim($params)) {
            $path .= "?$params";
        }
        $this->redirectUri("/$path");
    }

    public function redirectUri(string $uri)
    {
        header("Location: $uri");
        die();
    }

    /**
     * Установка инстанса текушего исполняемого контроллера
     *
     * @param ControllerInterface $controller
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setCurrentController(ControllerInterface $controller)
    {
        $this->_currentController = $controller;
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
     * @param RequestInterface $request
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setRequest(RequestInterface $request)
    {
        $this->_request = $request;
    }

    /**
     * Устанавливает инстанс ответа
     *
     * @param ResponseInterface $response
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->_response = $response;
    }

    /**
     * Устанавливает список роутов
     *
     * @param array $routes
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setRoutes(array $routes)
    {
        $this->_routes = $routes;
    }
}
