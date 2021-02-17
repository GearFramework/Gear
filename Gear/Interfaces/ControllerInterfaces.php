<?php

namespace Gear\Interfaces;

/**
 * Интерфейс api-методов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ApiInterface
{
    /**
     * Вызов метода $this->exec();
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke();

    /**
     * Вызов api-метода
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exec();
}

/**
 * Интерфейс контроллеров
 *
 * @package Gear Framework
 *
 * @property string apiRequest
 * @property iterable apis
 * @property string defaultApi
 * @property string layout
 * @property string name
 * @property RouterInterface owner
 * @property RequestInterface|null request
 * @property array requestModels
 * @property ResponseInterface|null response
 * @property string title
 * @property array|object viewSchema
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ControllerInterface
{
    /**
     * Вызов метода $this->exec()
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     * @since 0.0.1
     * @version 0.0.2
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response);

    /**
     * Возвращает экземпляр запроса
     *
     * @return RequestInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getRequest(): RequestInterface;

    /**
     * Возвращает экземпляр ответа
     *
     * @return ResponseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getResponse(): ResponseInterface;

    /**
     * Отображение шаблона
     *
     * @param $template
     * @param array $context
     * @param bool $buffered
     * @return bool|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function render($template, array $context = [], bool $buffered = false);

    /**
     * Запуск контроллера
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     * @since 0.0.1
     * @version 0.0.2
     */
    public function run(RequestInterface $request, ResponseInterface $response);
}

/**
 * Интерфейс роутера
 *
 * @package Gear Framework
 *
 * @property null|ControllerInterface controller
 * @property null|ControllerInterface currentController
 * @property string defaultController
 * @property string defaultControllersPath
 * @property RequestInterface request
 * @property string requestRoute
 * @property ResponseInterface response
 * @property bool rewrite
 * @property array routes
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
interface RouterInterface
{

    /**
     * Добавляет соответствие пути к классу контроллера
     *
     * @param string $path
     * @param string|array $class
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function addRoute(string $path, $class);

    /**
     * Добавляет массив соответствий путей к соответствующим классам контроллеров
     *
     * @param iterable $routes
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function addRoutes(iterable $routes);

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
    public function exec(RequestInterface $request, ResponseInterface $response);

    /**
     * Возвращает название класса контроллера по указанному пути
     *
     * @param string $route
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getClassByRoute(string $route): string;

    /**
     * Возвращает название класса контроллера по указанному пути в списке путей
     *
     * @param string $route
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getClassByRouteInRoutes(string $route): string;

    /**
     * Возвращает текущий контроллер, если таковой определен
     *
     * @return ControllerInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getController(): ControllerInterface;

    /**
     * Возвращает инстантс или NULL текушего контроллера
     *
     * @return ControllerInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCurrentController(): ?ControllerInterface;

    /**
     * Возвращает название контроллера, запускаемого по-умолчанию
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultController(): string;

    /**
     * Возвращает путь к папке с контроллерами
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultControllersPath(): string;

    /**
     * Возвращает инстанс запроса
     *
     * @return RequestInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getRequest(): RequestInterface;

    /**
     * Возвращает маршрут из запроса пользователя
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRequestRoute(): string;

    /**
     * Возвращает инстанс ответа
     *
     * @return ResponseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getResponse(): ResponseInterface;

    /**
     * Возвращает список роутов
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRoutes(): array;

    /**
     * Возвращает true, если включен mod_rewrite/nginx rewrite module
     *
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function isRewriteOn(): bool;

    /**
     * Переход по укзанному роуту с указанными параметрами
     *
     * @param string $path
     * @param array $params
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function redirect(string $path, array $params = []);

    /**
     * Переход по укзанному uri
     *
     * @param string $uri
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function redirectUri(string $uri);
}