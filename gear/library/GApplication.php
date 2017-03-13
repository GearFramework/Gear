<?php

namespace gear\library;

use gear\Core;
use gear\interfaces\IRequest;
use gear\interfaces\IResponse;

/**
 * Модуль приложений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GApplication extends GModule
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [
        'components' => [
            'controllers' => [
                'class' => '\gear\components\controllers\GControllersComponent',
            ],
        ],
        'plugins' => [
            'request' => ['class' => '\gear\plugins\http\GRequestPlugin'],
            'response' => ['class' => '\gear\plugins\http\GResponsePlugin'],
            'uri' => ['class' => '\gear\plugins\http\GUriPlugin'],
        ],
    ];
    protected static $_initialized = false;
    /* Public */

    /**
     * Генерация события после исполнения приложения
     * 
     * @param $result
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterRunApplication($result)
    {
        return Core::trigger('afterRunApplication', new GEvent($this, ['result' => $result]));
    }

    /**
     * Генерация событие перед запуском приложения
     *
     * @param IRequest $request
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeRunApplication(IRequest $request)
    {
        return Core::trigger('beforeRunApplication', new GEvent($this, ['request' => $request]));
    }

    /**
     * Завершение работы приложения
     *
     * @param IResponse $response
     * @param $result
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function end(IResponse $response, $result)
    {
        $response->send($result);
        return Core::trigger('onEndApplication', new GEvent($this, ['response' => $response, 'result' => &$result]));
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
    }

    /**
     * Запуск приложения
     *
     * @param IRequest|null $request
     * @param IResponse|null $response
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function run(IRequest $request = null, IResponse $response = null)
    {
        Core::syslog(Core::INFO, 'Run application <{app}>', ['app' => get_class($this), '__func__' => __METHOD__, '__line__' => __LINE__]);
        $result = null;
        if (!$request)
            $request = $this->request;
        if (!$response)
            $response = $this->response;
        if ($this->beforeRunApplication($request)) {
            $result = $this->controllers->exec($request);
        }
        return $this->end($response, $result);
    }
}