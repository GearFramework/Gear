<?php

namespace Gear\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Интерфейс плагина-обёртки для обработки и подготовки данных запросов для контроллеров
 *
 * @package Gear Framework
 *
 * @property ControllerInterface controller
 * @property ControllerInterface owner
 * @property RequestInterface request
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
interface ControllerRequestInterface {}

/**
 * Интерфейс модели с данными запроса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface RequestDataInterface
{
    /**
     * Валидация значения
     *
     * @param string $name
     * @param string|null $value
     * @return mixed|string|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function validate(string $name, $value);
}

/**
 * Интерфейс плагина для работы с запросами
 *
 * @package Gear Framework
 *
 * @property array|null cli
 * @property string defaultMethod
 * @property array|null files
 * @property string method
 * @property array orders
 * @property ApplicationInterface owner
 * @property string remoteAddress
 * @property string remoteHost
 * @property array requestHandlers
 * @property array variablesOrders
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface RequestInterface extends ServerRequestInterface
{
    /**
     * Возвращает параметры текущего запроса (GET или POST)
     *
     * @param string $name
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke(string $name = '');

    /**
     * Обработка и получение параметров командой строки
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function cli(string $name = '', $default = null);

    /**
     * Обработка cookie
     *
     * @param string $name
     * @param array ...$params
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function cookie(string $name = '', ...$params);

    /**
     * Возвращает параметры get-запроса или значение указанного параметра
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function get(string $name = '', $default = null);

    /**
     * Возвращает метод по-умолчанию
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultMethod(): string;

    /**
     * Возвращает нормализованный список загруженных файлов на сервер
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getFiles(): array;

    /**
     * Возвращает метод запроса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMethod(): string;

    /**
     * Возвращает адрес клиента, пославшего запрос
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRemoteAddress(): string;

    /**
     * Возвращает имя удаленного хоста
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRemoteHost(): string;

    /**
     * Возвращает true, если искомый параметр передан в коммандной строке
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inCli(string $name): bool;

    /**
     * Возвращает true, если искомый параметр находится в cookie
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inCookie(string $name): bool;

    /**
     * Возвращает true, если искомый параметр передан в GET запросе
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inGet(string $name): bool;

    /**
     * Возвращает true, если искомый параметр находится среди загруженных файлов
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inFiles(string $name): bool;

    /**
     * Возвращает true, если искомый параметр передан в POST запросе
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inPost(string $name): bool;

    /**
     * Возвращает true, если искомый параметр сохранен в PHP-сессии
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inSession(string $name): bool;

    /**
     * Возвращает true, если был сделан ajax-запрос
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isAjax(): bool;

    /**
     * Возвращает true, если запрос сделан из консоли
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isCli(): bool;

    /**
     * Возвращает true, если запрос пришёл с настольного компьютера
     *
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function isDesktop(): bool;

    /**
     * Возвращает true, если сделан GET-запрос
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isGet(): bool;

    /**
     * Возвращает true, если используется обычный http-протокол
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isHttp(): bool;

    /**
     * Возвращает true, если используется https
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isHttps(): bool;

    /**
     * Возвращает true, если запрос пришёл с мобильного телефона
     *
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function isMobile(): bool;

    /**
     * Возвращает true, если сделан POST-запрос
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isPost(): bool;

    /**
     * Возвращает true, если запрос пришёл с планшета
     *
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function isTablet(): bool;

    /**
     * Возвращает значение указанного параметра, независимо от метода запроса
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function param(string $name, $default = null);

    /**
     * Работа с параметрами GET запроса
     *
     * @param string $name
     * @param null $default
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function post(string $name = '', $default = null);

    /**
     * Возвращает параметры cессии
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function session(string $name = '', $value = null);

    /**
     * Возвращает загруженные файлы
     *
     * @param string $name
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uploads(string $name = '');
}

/**
 * Интерфейс плагина для работы с ответами на пользовательские запросы
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ResponseInterface extends \Psr\Http\Message\ResponseInterface
{
    /**
     * Отправляет клиенту данные
     *
     * @param mixed $data
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function send($data);

    /**
     * Отправка файла
     *
     * @param FileInterface $file
     * @param array $headers
     * @param int $speed
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function sendFile(FileInterface $file, array $headers = [], $speed = 0);

    /**
     * Отправка установленных заголовков
     *
     * @return ResponseInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sendHeaders(): ResponseInterface;

    /**
     * Отправка заголовка-ответа с указанным статусом
     *
     * @param $code
     * @return ResponseInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sendStatus($code): ResponseInterface;
}

interface UriInterface extends \Psr\Http\Message\UriInterface
{

}
