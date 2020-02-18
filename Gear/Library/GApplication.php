<?php

namespace Gear\Library;

use Gear\Core;
use Gear\Interfaces\ModuleInterface;
use Gear\Interfaces\RequestInterface;
use Gear\Interfaces\RouterInterface;

/**
 * Класс приложений
 *
 * @package Gear Framework
 *
 * @property RequestInterface request
 * @property RequestInterface response
 * @property RouterInterface router
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GApplication extends GModule implements ModuleInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'components' => [
            'router' => [
                'class' => '\Gear\Components\Router\GRouterComponent',
            ],
        ],
        'plugins' => [
            'request' => ['class' => '\Gear\Plugins\Http\GRequest'],
            'response' => ['class' => '\Gear\Plugins\Http\GResponse'],
        ],
    ];
    /* Public */

    /**
     * Запускается перед завершении работы приложения
     *
     * @param mixed $result
     * @return mixed
     * @since 0.0.1
     * @version 0.0.2
     */
    public function afterRun($result)
    {
        return Core::onAfterRunApplication(new GEvent($this, ['result' => $result]));
    }

    /**
     * Запускается перед началом работы приложения
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeRun()
    {
        return Core::onBeforeRunApplication(new GEvent($this));
    }

    /**
     * Завершение работы приложения
     *
     * @param mixed $result
     * @return void
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    final public function end($result)
    {
        $this->afterRun($result);
        $this->response->send($result);
        exit(0);
    }

    public function redirect($path, $params = [])
    {
        $this->router->redirect($path, $params);
    }

    public function redirectUri(string $uri)
    {
        $this->router->redirectUri($uri);
    }

    /**
     * Запуск приложения
     *
     * @return void
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    final public function run()
    {
        if ($this->beforeRun()) {
            $result = $this->router->exec($this->request, $this->response);
            $this->end($result);
        }
    }
}
