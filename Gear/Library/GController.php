<?php

namespace Gear\Library;

use Gear\Core;
use Gear\Interfaces\ApiInterface;
use Gear\Interfaces\ControllerInterface;
use Gear\Interfaces\ControllerRequestInterface;
use Gear\Interfaces\RequestInterface;
use Gear\Interfaces\ResponseInterface;
use Gear\Interfaces\RouterInterface;

/**
 * Контроллер
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
class GController extends GModel implements ControllerInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected string $_apiRequest = '';
    protected array $_apis = [];
    protected string $_defaultApi = 'index';
    protected string $_layout = '';
    protected ?RequestInterface $_request = null;
    protected array $_requestModels = [];
    protected ?ResponseInterface $_response = null;
    protected string $_title = 'Title';
    protected $_viewSchema = [];
    /* Public */

    /**
     * Вызов метода $this->run()
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     * @throws \ReflectionException
     * @uses $this->run()
     * @since 0.0.1
     * @version 0.0.2
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response)
    {
        return $this->run($request, $response);
    }

    /**
     * Генерация события после того как контроллер отработал
     *
     * @param mixed $result
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterRun($result)
    {
        return Core::trigger('onAfterRun', new GEvent($this, ['result' => $result]));
    }

    /**
     * Генерация события перед тем как контроллер начнёт работу
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeRun()
    {
        $event = new GEvent($this);
        if ($result = $this->trigger('onBeforeRun', $event)) {
            $result = Core::trigger('onBeforeRun', $event);
        }
        return $result;
    }

    /**
     * Генерация события перед тем как контроллер выполнит api-метод
     *
     * @param string $api
     * @param string $apiMethod
     * @param array $params
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeExecApi(string $api, string $apiMethod, array $params)
    {
        $event = new GEvent($this, ['r' => $api, 'api' => $apiMethod, 'params' => $params]);
        if ($result = $this->trigger('onBeforeExecApi', $event)) {
            $result = Core::trigger('onBeforeExecApi', $event);
        }
        return $result;
    }

    public function getApiRequest(): string
    {
        return $this->_apiRequest;
    }

    /**
     * Возвращает список параметров, которые требует указанный api-метод
     *
     * @param ApiInterface|\Closure|string|array $method
     * @return array
     * @throws \ReflectionException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getApiParams($method): array
    {
        if ($method instanceof \Closure)
            $reflection = new \ReflectionFunction($method);
        elseif (is_string($method)) {
            $reflection = new \ReflectionMethod($this, $method);
        } elseif ($method instanceof ApiInterface) {
            $reflection = new \ReflectionMethod($method, 'exec');
        } else {
            list($instance, $apiName) = $method;
            $reflection = new \ReflectionMethod($instance, $apiName);
        }
        $params = $reflection->getParameters();
        $result = [];
        $method = strtolower($this->request->getMethod());
        foreach ($params as $param) {
            $value = $this->request->$method($param->name);
            if ($value === null) {
                if (!$param->isOptional()) {
                    throw self::InvalidApiParamsException();
                }
                $value = $param->getDefaultValue();
            }
            $result[] = $value;
        }
        return $result;
    }

    /**
     * Возвращает массив api-методов контроллера
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getApis(): array
    {
        return $this->_apis;
    }

    /**
     * Возвращает название api-метода, выполняемого по-умолчанию
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDefaultApi(): string
    {
        return $this->_defaultApi;
    }

    /**
     * Возвращает путь к основному шаблону отображения
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getLayout(): string
    {
        return $this->_layout;
    }

    /**
     * Возвращает экземпляр запроса
     *
     * @return RequestInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getRequest(): RequestInterface
    {
        return $this->_request;
    }

    public function getRequestModels(): array
    {
        return $this->_requestModels;
    }

    /**
     * Возвращает экземпляр ответа
     *
     * @return ResponseInterface|ControllerRequestInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getResponse(): ResponseInterface
    {
        return $this->_response;
    }

    /**
     * Возвращает название страницы, которую обслуживает контролле
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getTitle(): string
    {
        return $this->_title;
    }

    public function getViewSchema(): object
    {
        if (!is_object($this->_viewSchema)) {
            $this->_viewSchema = (object)$this->_viewSchema;
        }
        return $this->_viewSchema;
    }

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
    public function render($template, array $context = [], bool $buffered = false)
    {
        $viewer = $this->{$this->viewerName};
        if ($this->layout) {
            if (is_array($template)) {
                $contentPage = [];
                foreach($template as $sectionName => $templateName) {
                    $contentPage[$sectionName] = $viewer->render($templateName, $context, true);
                }
            } else {
                $contentPage = ['contentLayout' => $viewer->render($template, $context, true)];
            }
            $result = $viewer->render($this->layout, array_merge($context, $contentPage), $buffered);
        } else {
            $result = $viewer->render($template, $context, $buffered);
        }
        return $result;
    }

    /**
     * Начало работы контроллера
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     * @throws \ReflectionException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        $result = null;
        $api = '';
        $this->request = $request;
        $this->response = $response;
        if ($this->beforeRun()) {
            $route = $request->param('r', $this->defaultApi);
            preg_match('#/a(($)|(/[A-Za-z0-9_]*)+)#', $route, $match);
            if ($match) {
                $api = trim($match[1], "/");
            }
            $this->_apiRequest = $api = $api ?: $this->_defaultApi;
            $apis = $this->apis;
            if (isset($apis[$api])) {
                $apiMethod = $apis[$api];
            } else {
                $apiMethod = 'api' . ucfirst($api);
            }
            $params = $this->getApiParams($apiMethod);
            if ($this->beforeExecApi($api, $apiMethod, $params)) {
                if (is_string($apiMethod)) {
                    $apiMethod = [$this, $apiMethod];
                }
                $result = call_user_func($apiMethod, ...$params);
            }
            $this->afterRun($result);
        }
        return $result;
    }

    /**
     * Установка массива api-методов
     *
     * @param \Closure|array $apis
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setApis($apis)
    {
        if ($apis instanceof \Closure) {
            $apis = $apis($this);
        }
        if (!is_array($apis)) {
            throw self::InvalidApisException();
        }
        $this->_apis = $apis;
    }

    /**
     * Установка названия исполняемого api-метода
     *
     * @param string $apiName
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDefaultApi(string $apiName)
    {
        $this->_defaultApi = $apiName;
    }

    /**
     * Установка пути к основному шаблону отображения
     *
     * @param string $layout
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setLayout(string $layout)
    {
        $this->_layout = $layout;
    }

    /**
     * Установка запроса
     *
     * @param RequestInterface $request
     * @return void
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setRequest(RequestInterface $request)
    {
        if ($this->isPluginRegistered('controllerRequest')) {
            $this->_request = $this->p('controllerRequest');
        } else {
            $this->_request = $request;
        }
    }

    /**
     * Установка ответа
     *
     * @param ResponseInterface $response
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->_response = $response;
    }
}
