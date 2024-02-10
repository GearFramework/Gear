<?php

namespace Gear\Traits\Objects;

use Gear\Interfaces\Services\PluginContainedInterface;
use Gear\Interfaces\Templater\TemplateInterface;
use Gear\Interfaces\Templater\ViewerInterface;
use Gear\Interfaces\Templater\ViewOptionsInterface;
use Stringable;

/**
 * Трэйт для рендеринга шаблонов
 *
 * @package Gear Framework
 *
 * @property string             $viewerName
 * @property string|Stringable  $viewLayout
 * @property array              $renderSchemas
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
trait ViewableTrait
{
    /**
     * Получение названия шаблонизатора, записанного в конфигурации класса
     *
     * @return string
     */
    public function getViewerName(): string
    {
        return $this->viewerName;
    }

    /**
     * Установка названия шаблонизатора, записанного в конфигурации класса
     *
     * @param string $name
     * @return void
     */
    public function setViewerName(string $name): void
    {
        $this->viewerName = $name;
    }

    /**
     * Возвращает путь, по которому лежат шаблоны отображения объекта
     *
     * @return string|Stringable
     */
    public function getViewPath(): string|Stringable
    {
        return $this->viewPath;
    }

    /**
     * Установка пути, по которому лежат шаблоны отображения объекта
     *
     * @param string|Stringable $viewPath
     * @return void
     */
    public function setViewPath(string|Stringable $viewPath): void
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Возвращает плагин шаблонизатора или null если он не установлен
     *
     * @return ViewerInterface|null
     */
    public function getViewer(): ?ViewerInterface
    {
        $viewerPluginName = $this->getViewerName();
        /** @var PluginContainedInterface $this */
        /** @var ViewerInterface $viewerPlugin */
        $viewerPlugin = $this->p($viewerPluginName);
        return $viewerPlugin;
    }

    /**
     * Отображение шаблона
     *
     * @param string|Stringable|TemplateInterface   $template
     * @param array                                 $context
     * @param null|ViewOptionsInterface             $options
     * @return bool|string
     */
    public function render(
        string|Stringable|TemplateInterface $template,
        array $context = [],
        ?ViewOptionsInterface $options = null,
    ): bool|string {
        $viewerPlugin = $this->getViewer();
        if (empty($viewerPlugin)) {
            return false;
        }
        return $viewerPlugin->render($template, $context, $options);
    }
}
