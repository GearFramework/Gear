<?php

namespace Gear\Components\Router;

use Gear\Interfaces\ApplicationInterface;
use Gear\Interfaces\ControllerInterface;
use Gear\Interfaces\Http\HttpInterface;
use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ServerInterface;
use Gear\Interfaces\Objects\ModelInterface;
use Gear\Interfaces\RouterInterface;
use Gear\Library\Services\Component;
use Gear\Traits\Http\HttpTrait;

/**
 * Компонент роутинга запросов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class RouterComponent extends Component implements RouterInterface, HttpInterface
{
    /* Traits */
    use HttpTrait;
    /* Const */
    /* Private */
    private ApplicationInterface $application;
    /* Protected */
    protected ?ControllerInterface $controller = null;
    protected string $defaultControllerName = 'index';
    protected null|ModelInterface|ApplicationInterface $owner = null;
    protected array $routes = [];
    /* Public */

    /**
     * Установка обработчика GET-запроса по указанному роуту
     *
     * @param   string    $route
     * @param   callable  $handler
     * @return  void
     */
    public function get(string $route, callable $handler): void
    {
        $this->addRoute(self::GET, $route, $handler);
    }

    /**
     * Установка обработчика POST-запроса по указанному роуту
     *
     * @param   string    $route
     * @param   callable  $handler
     * @return  void
     */
    public function post(string $route, callable $handler): void
    {
        $this->addRoute(self::POST, $route, $handler);
    }

    /**
     * Установка обработчика PUT-запроса по указанному роуту
     *
     * @param   string    $route
     * @param   callable  $handler
     * @return  void
     */
    public function put(string $route, callable $handler): void
    {
        $this->addRoute(self::PUT, $route, $handler);
    }

    /**
     * Установка обработчика DELETE-запроса по указанному роуту
     *
     * @param   string    $route
     * @param   callable  $handler
     * @return  void
     */
    public function delete(string $route, callable $handler): void
    {
        $this->addRoute(self::DELETE, $route, $handler);
    }

    /**
     * Установка обработчика указанного роута и метода запроса
     *
     * setRoute('POST', 'article/a/edit/{int id}', [Article::class, 'edit'])
     * setRoute('GET', 'article/a/edit', Article::class])
     *
     * @param   string            $method
     * @param   string            $route
     * @param   string|callable   $handler
     * @return  void
     */
    public function addRoute(string $method, string $route, string|callable $handler): void
    {
        $this->routes[$route][$method] = $handler;
    }

    /**
     * Возвращает массив установленных роутов
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Устанавливает переданный массив роутов
     *
     * @param   array $routes
     * @return  bool
     */
    public function setRoutes(array $routes): bool
    {
        $this->routes = $routes;
        return true;
    }

    /**
     * Возвращает название контроллера, который выполняется
     * по-умолчанию, если не указан роут
     *
     * @return string
     */
    public function getDefaultControllerName(): string
    {
        return $this->defaultControllerName;
    }

    /**
     * Установка названия контроллера, который будет использоваться по-умолчанию
     *
     * @param   string $controllerName
     * @return  bool
     */
    public function setDefaultControllerName(string $controllerName): bool
    {
        $this->defaultControllerName = $controllerName;
        return true;
    }

    /**
     * Получение роута и запуск соответствующего экшена
     *
     * @return  callable
     */
    public function getAction(ApplicationInterface $application): callable
    {
        $this->application = $application;
        return $this->getActionFromRequest();
    }

    /**
     * Возвращает экшен, который должен быть выполнен согласно указанного
     * в запросе роута
     *
     * @return callable
     */
    protected function getActionFromRequest(): callable
    {
        $uri = $this->getUriFromRequest();
        if (str_starts_with($uri, '/')) {
            $uri = substr($uri, 1);
        }
        return $this->getActionByRoute($uri);
    }

    private function getUriFromRequest(): string
    {
        if ($this->isRewriteOn()) {
            return parse_url($this->server->requestUri, PHP_URL_PATH);
        }
        $controllerRoute = $this->request->get('r');
        $actionRoute = $this->request->get('a');
        return ($controllerRoute ?: '/') . ($actionRoute ? "/a/{$actionRoute}" : '');
    }

    /**
     * Возвращает инстанс контроллера по указанному роуту
     *
     * @param   string $uri
     * @return  callable|int|array
     */
    protected function getActionByRoute(string $uri): callable|int|array
    {
        $uriParts = explode('/a/', $uri);
        $controllerRoute = isset($uriParts[0]) && $uriParts[0]
            ? $uriParts[0]
            : $this->getDefaultControllerName();
        $actionRoute = $uriParts[1] ?? '';
        $manualAction = $this->hasManualRoute($uri);
        if (is_int($manualAction) || is_callable($manualAction)) {
            return $manualAction;
        }
        $controller = $this->getControllerByRoute($controllerRoute);
        return [$controller, $actionRoute];
    }

    private function hasManualRoute(string $uri): false|int|callable
    {
        return $this->getManualRouteAction(explode('/', $uri));
    }

    protected function getManualRouteAction(array $partsRoute): false|int|callable
    {
        $requestMethod = $this->request->getRequestMethod();
        $routes = $this->getRoutes();
        while ($partsRoute) {
            $path = implode('/', $partsRoute);
            if (isset($routes[$path]) === false) {
                array_pop($partsRoute);
                continue;
            }
            list($controllerMethod, $handler) = $routes[$path];
            return $controllerMethod === $requestMethod
                ? $handler
                : HttpInterface::HTTP_STATUS_BAD_REQUEST;
        }
        return false;
    }

    private function getControllerByRoute(string $controllerRoute): ControllerInterface
    {
        $partsRoute = explode('/', $controllerRoute);
        $partsClassPath = [];
        foreach ($partsRoute as $itemRoute) {
            $partsClassPath[] = ucfirst($itemRoute);
        }
        $projectNamespace = $this->owner->namespace;
        $classPath = implode('\\', $partsClassPath);
        $className = "\\{$projectNamespace}\\Controllers\\$classPath";
        return new $className([
            'route' => $controllerRoute,
        ], $this);
    }

    /**
     * Возвращает true, если включен режим ЧПУ
     *
     * @return bool
     */
    public function isRewriteOn(): bool
    {
        return $this->getRewriteOn() === true;
    }

    /**
     * Возвращает состояние режима ЧПУ
     *
     * @return bool
     */
    public function getRewriteOn(): bool
    {
        return (bool)$this->props('rewriteOn');
    }

    /**
     * Установка состояния режима ЧПУ
     *
     * @param   bool $rewriteOn
     * @return  void
     */
    public function setRewriteOn(bool $rewriteOn): void
    {
        $this->props('rewriteOn', $rewriteOn);
    }

    /**
     * Переход по указанному роуту с параметрами и статусом http
     *
     * @param   string $path
     * @param   array  $params
     * @param   int    $status
     * @return  void
     */
    public function redirect(
        string $path,
        array $params = [],
        int $status = self::HTTP_STATUS_MOVED_PERMANENTLY
    ): void {
        $getParams = $this->prepareParams($params);
        $path = $getParams ? "?{$getParams}" : $path;
        $this->redirectUri(str_starts_with($path, '/') ? $path : "/$path", $status);
    }

    /**
     * Подготовка параметров запроса для адресной строки
     *
     * @param   array $getParams
     * @return  string
     */
    protected function prepareParams(array $getParams): string
    {
        $result = [];
        foreach($getParams as $name => $value) {
            $result[] = "{$name}=" . urlencode($value);
        }
        return implode('&', $result);
    }

    /**
     * Переход по указанному uri
     *
     * @param   string $uri
     * @param   int    $status
     * @return  void
     */
    public function redirectUri(string $uri, int $status = self::HTTP_STATUS_MOVED_PERMANENTLY): void
    {
        header("Location: {$uri}", true, $status);
        exit(0);
    }
}
