<?php

namespace Gear\Traits;

/**
 * Трэйт для рендеринга шаблонов
 *
 * @package Gear Framework
 *
 * @property string viewerName
 * @property string viewLayout
 * @property string viewPath
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait ViewTrait
{
    /**
     * @var string $_viewerName название плагина выступающего в качестве шаблонизатора
     */
    protected string $_viewerName = 'view';

    /**
     * Получение названия шаблонизатора, записанного в конфигурации класса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getViewerName(): string
    {
        return $this->_viewerName;
    }

    /**
     * Возвращает путь, по которому лежат шаблоны отображения объекта
     *
     * @return string|DirectoryInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getViewPath()
    {
        return $this->_viewPath;
    }

    /**
     * Возвращает путь, по которому лежит основной макет
     *
     * @return string|DirectoryInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getViewLayout()
    {
        return $this->_viewLayout;
    }

    /**
     * Возвращает карту расположения шаблонов
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getViewsMap(): array
    {
        return $this->_viewsMap;
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
        return $this->{$this->viewer}->render($template, $context, $buffered);
    }

    /**
     * Подключение .phtml файла с шаблоном отображения
     *
     * @param string $filePath
     * @param array $context
     * @param bool $buffered
     * @return bool|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function renderFile(string $filePath, array $context = [], bool $buffered = false)
    {
        return $this->{$this->viewer}->renderFile($filePath, $context, $buffered);
    }

    /**
     * Утсновка названия шаблонизатора, записанного в конфигурации класса
     *
     * @param string $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setViewerName(string $name)
    {
        $this->_viewerName = $name;
    }

    /**
     * Установка пути, по которому лежит основной макет
     *
     * @access public
     * @param string|DirectoryInterface $viewLayout
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setViewLayout($viewLayout)
    {
        $this->_viewLayout = $viewLayout;
    }

    /**
     * Установка пути, по которому лежат шаблоны отображения объекта
     *
     * @access public
     * @param string|DirectoryInterface $viewPath
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setViewPath($viewPath)
    {
        $this->_viewPath = $viewPath;
    }

    /**
     * Установка карты расположения шаблонов
     *
     * @param array $viewsMap
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function setViewsMap(array $viewsMap): void
    {
        $this->_viewsMap = $viewsMap;
    }
}
