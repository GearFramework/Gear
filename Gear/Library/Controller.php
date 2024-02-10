<?php

namespace Gear\Library;

use Gear\Core;
use Gear\Exceptions\Http\NotFoundException;
use Gear\Interfaces\ControllerInterface;
use Gear\Interfaces\Http\HttpInterface;
use Gear\Interfaces\Http\RequestInterface;
use Gear\Interfaces\Http\ResponseInterface;
use Gear\Interfaces\Http\ServerInterface;
use Gear\Interfaces\Objects\ModelInterface;
use Gear\Interfaces\RouterInterface;
use Gear\Interfaces\Templater\TemplateInterface;
use Gear\Interfaces\Templater\ViewOptionsInterface;
use Gear\Library\Objects\Entity;
use Gear\Plugins\Templater\Viewer;
use Gear\Traits\Http\HttpTrait;
use Stringable;

/**
 * Класс контроллеров
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
abstract class Controller extends Entity implements ControllerInterface
{
    /* Traits */
    use HttpTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected static array $config = [
        'plugins' => [
            'view' => [
                'class'             => Viewer::class,
                'viewLayout'        => '\Gear\Views\Common\Layout',
                'templateAliases'   => [],
                'renderSchemas'     => [],
            ],
        ],
    ];
    protected string $actionName = '';
    protected string $defaultActionName = 'index';
    protected null|ModelInterface|RouterInterface $owner;
    protected array $actionRoutes = [
        'index/{string id}' => 'index',
    ];
    protected RequestInterface $request;
    protected ResponseInterface $response;
    protected ServerInterface $server;
    /* Public */
    public string $title = 'Page title';

    /**
     * Вызов экшена контроллера
     *
     * @param   string            $actionRoute
     * @param   ResponseInterface $response
     * @return  mixed
     */
    public function __invoke(string $actionRoute, ResponseInterface $response): mixed
    {
        return $this->invoke($actionRoute, $response);
    }

    /**
     * Вызов экшена контроллера
     *
     * @param   string            $actionRoute
     * @param   ResponseInterface $response
     * @return  mixed
     */
    public function invoke(string $actionRoute, ResponseInterface $response): mixed
    {
        $this->response = $response;
        list($actionName, $params) = $this->getActionNameFromRoute($actionRoute);
        if (empty($actionName)) {
            return HttpInterface::HTTP_STATUS_NOT_FOUND;
        }
        $this->actionName = $actionName;
        $callableMethodName = 'action' . ucfirst($actionName);
        if (method_exists($this, $callableMethodName) === false) {
            return HttpInterface::HTTP_STATUS_NOT_FOUND;
        }
        return $this->$callableMethodName(...$params);
    }

    /**
     * Возвращает название экшена контроллера со списком параметров, который
     * соответствует указанному роуту в запросе
     *
     * @param   string $actionRoute
     * @return  array
     */
    protected function getActionNameFromRoute(string $actionRoute): array
    {
        if ($actionRoute === '') {
            return [$this->getDefaultActionName(), []];
        }
        $actionRouteParts = explode('/', $actionRoute);
        $actionName = array_shift($actionRouteParts);
        return [$actionName, $actionRouteParts];
    }

    /**
     * Возвращает название экшена, вызываемого по-умолчанию
     *
     * @return string
     */
    public function getDefaultActionName(): string
    {
        return $this->defaultActionName;
    }

    /**
     * Установка названия экшена, вызываемого по-умолчанию
     *
     * @param   string $defaultActionName
     * @return  void
     */
    public function setDefaultActionName(string $defaultActionName): void
    {
        $this->defaultActionName = $defaultActionName;
    }

    /**
     * Возвращает название текущего экшена
     *
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * Рендеринг указанного html-шаблона
     * Если шаблон не задан, то используется шаблон из
     * схемы рендеринга текущего экшена
     *
     * @param   string|TemplateInterface|Stringable   $template
     * @param   array                                 $context
     * @param   null|ViewOptionsInterface             $options
     * @return  bool|string
     */
    public function render(
        string|TemplateInterface|Stringable $template = '',
        array $context = [],
        ?ViewOptionsInterface $options = null,
    ): bool|string {
        return $this->getViewer()->render((string)$template, $context, $options);
    }
}
