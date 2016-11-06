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
        return Core::trigger('onEndApplication', new GEvent($this, ['response' => $response, 'result' => &$result]));
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
        $result = null;
        if (!$request)
            $request = $this->request();
        if (!$response)
            $response = $this->response();
        if ($this->beforeRunApplication($request)) {
            $result = $this->controllers->exec();
        }
        return $this->end($response, $result);
    }
}