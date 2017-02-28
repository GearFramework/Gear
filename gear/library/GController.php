<?php

namespace gear\library;

use gear\Core;
use gear\interfaces\IController;
use gear\interfaces\IRequest;

/**
 * Класс контроллеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GController extends GModel implements IController
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /**
     * @var IRequest $_request
     */
    protected $_request = null;
    protected $_defaultApiName = 'index';
    protected $_mapApi = [];
    /* Public */

    /**
     * Запуск контроллера
     *
     * @param IRequest $request
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __invoke(IRequest $request)
    {
        return $this->exec($request);
    }

    public function afterExecApi($runApi, $result)
    {
        if (is_array($runApi))
            list($api,) = $runApi;
        else
            $api = $runApi;
        return Core::trigger('onAfterExecApi', new GEvent($this, ['target' => $api, 'result' => $result]));
    }

    public function afterExecController($result)
    {
        return Core::trigger('onAfterExecController', new GEvent($this, ['target' => $this, 'result' => $result]));
    }

    public function beforeExecApi($api, $runApi, $params)
    {
        if (is_array($runApi))
            list($runApi,) = $runApi;
        return Core::trigger('onBeforeExecApi', new GEvent($this, ['target' => $api, 'api' => $runApi, 'params' => $params]));
    }

    public function beforeExecController()
    {
        return Core::trigger('onBeforeExecController', new GEvent($this, ['target' => $this]));
    }

    /**
     * Запуск контроллера
     *
     * @param IRequest $request
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exec(IRequest $request)
    {
        $this->request = $request;
        $result = false;
        if ($this->beforeExecController()) {
            $api = $this->getRouteApi((string)$this->request->r);
            $mapApi = $this->existsInMapApi($api, true);
            if ($mapApi) {
                if ($mapApi instanceof \Closure) {
                    $runApi = $mapApi;
                } else {
                    $class = $mapApi;
                    try {
                        $runApi = [new $class(['name' => $api], $this), 'exec'];
                    } catch(\Exception $e) {
                        throw static::exceptionHttpNotFound();
                    }
                }
            } else {
                $name = 'api' . ucfirst($api);
                if (!method_exists($this, $name))
                    throw static::exceptionHttpNotFound(['uri' => $api]);
                $runApi = [$this, $name];
            }
            $params = $this->getApiParams($runApi);
            if ($params === null) {
                throw static::exceptionHttpBadRequest(['uri' => $api]);
            }
            $result = false;
            if ($this->beforeExecApi($api, $runApi, $params)) {
                $result = call_user_func_array($runApi, $params);
            }
            $this->afterExecApi($runApi, $result);
        }
        $this->afterExecController($result);
    }

    /**
     * Возвращает true, если api-метод присутствует в карте, иначе false
     *
     * @param string $name
     * @param bool $returnPath
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function existsInMapApi(string $name, bool $returnPath = false)
    {
        if ($returnPath) {
            $result = isset($this->_mapApi[$name]) ? $this->_mapApi[$name] : false;
        } else {
            $result = in_array($name, $this->_mapApi, true);
        }
        return $result;
    }

    /**
     * Возвращает массив параметров, которые требует api-метод
     *
     * @param mixed $api
     * @return array|null
     * @throws \HttpBadRequest
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getApiParams($api)
    {
        if ($api instanceof \Closure)
            $reflection = new \ReflectionFunction($api);
        else {
            list($instance, $apiName) = $api;
            $reflection = new \ReflectionMethod($instance, $apiName);
        }
        $params = $reflection->getParameters();
        $result = [];
        foreach ($params as $param) {
            $value = $this->request->{$param->name};
            if ($value === null) {
                if (!$param->isOptional()) {
                    $result = null;
                    break;
                }
                $value = $param->getDefaultValue();
            }
            $result[] = $value;
        }
        return $result;
    }

    /**
     * Возвращает название api-метода, выполняемого по-умолчанию
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultApiName(): string
    {
        return $this->_defaultApiName;
    }

    /**
     * Возвращает карту путей к api-методам
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMapApi(): array
    {
        return $this->_mapApi;
    }

    /**
     * Возвращает название api-метода из запроса
     *
     * @param string $path
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRouteApi(string $path): string
    {
        $path = preg_replace('#^' . preg_quote($this->name) . '/?#', '', $path);
        $path ? list($name,) = explode('/', $path) : $name = $this->defaultApiName;
        return $name;
    }

    /**
     * Устанавливает карту путей к api-методам
     *
     * @param array $mapApi
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setMapApi(array $mapApi)
    {
        $this->_mapApi = $mapApi;
    }

    /**
     * Установка запроса
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
     * Возвращает экземпляр запроса
     *
     * @return IRequest
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getRequest(): IRequest
    {
        return $this->_request;
    }
}
