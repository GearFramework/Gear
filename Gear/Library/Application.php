<?php

namespace Gear\Library;

/**
 * Класс приложений
 *
 * @package Gear Framework 2
 *
 * @property DeviceDetectComponentInterface device
 * @property RequestInterface request
 * @property ResponseInterface response
 * @property RouterInterface router
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
class Application extends GModule implements ApplicationInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static array $_config = [
        'components' => [
            'device' => [
                'class' => '\Gear\Components\DeviceDetect\DeviceDetectComponent',
            ],
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

    /**
     * Переход по укзанному роуту контроллера, экшена с указанными параметрами
     *
     * @param string|null $controller
     * @param string|null $action
     * @param array $params
     * @since 0.0.2
     * @version 0.0.2
     */
    public function routeTo(string $controller = null, string $action = null, array $params = []): void
    {
        $path = '';
        $temp = [];
        if ($controller) {
            $temp[] = $controller;
        }
        if ($action) {
            if (!$controller) {
                $temp[] = $this->router->defaultController;
            }
            $temp[] = $action;
        }
        if ($this->router->isRewriteOn()) {
            $path = implode('/a/', $temp);
        } else {
            $params['r'] = implode('/a/', $temp);
        }
        $this->redirect($path, $params);
    }

    /**
     * Переход по укзанному роуту с указанными параметрами
     *
     * @param string $path
     * @param array $params
     * @since 0.0.1
     * @version 0.0.1
     */
    public function redirect(string $path = '/', array $params = []): void
    {
        $this->router->redirect($path, $params);
    }

    /**
     * Переход по укзанному uri
     *
     * @param string $uri
     * @since 0.0.1
     * @version 0.0.1
     */
    public function redirectUri(string $uri): void
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
