<?php

namespace Gear\Library;

use Gear\Components\DeviceDetect\DeviceDetectComponent;
use Gear\Components\Router\RouterComponent;
use Gear\Interfaces\ApplicationInterface;
use Gear\Library\Services\Module;
use Gear\Plugins\Http\Request;
use Gear\Plugins\Http\Response;
use Gear\Plugins\Http\Server;

/**
 * Модуль приложений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Application extends Module implements ApplicationInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static array $config = [
        'components' => [
            'device' => ['class' => DeviceDetectComponent::class],
            'router' => ['class' => RouterComponent::class],
        ],
        'plugins' => [
            'request'  => ['class' => Request::class],
            'response' => ['class' => Response::class],
            'server'   => ['class' => Server::class],
        ],
    ];
    /* Public */

    /**
     * Запуск приложения
     * Возвращает код ошибки или 0 если ошибок нет
     *
     * @return int
     */
    public function run(): int
    {
        $beforeRun = $this->beforeRun();
        if ($beforeRun !== true) {
            return $beforeRun;
        }
        $action = $this->router->getAction($this->server, $this->request);
        if (is_int($action)) {
            return $this->end($result);
        }
        if (is_callable($action)) {
            return $this->end($action($this->response));
        }
        list($controller, $actionRoute) = $action;
        $result = $controller($actionRoute, $this->response);
        return $this->end($result);
    }

    /**
     * Выполняется до запуска приложения, возвращает код ошибки, если запуск не возможен
     * в случае успеха, возвращает true
     *
     * @return bool|int
     */
    public function beforeRun(): bool|int
    {
        return true;
    }

    public function end(mixed $result): int
    {
        $this->response->send($result);
        return 0;
    }
}
