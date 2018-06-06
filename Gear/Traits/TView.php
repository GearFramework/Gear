<?php

namespace Gear\Traits;

/**
 * Трэйт для рендеринга шаблонов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TView
{
    /**
     * @var string $_viewer название плагина выступающего в качестве шаблонизатора
     */
    protected $_viewerName = 'view';

    /**
     * Установка пути, по которому лежат шаблоны отображения объекта
     *
     * @access public
     * @param string|IDirectory $viewPath
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setViewPath($viewPath)
    {
        $this->_viewPath = $viewPath;
    }

    /**
     * Возвращает путь, по которому лежат шаблоны отображения объекта
     *
     * @return string|IDirectory
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getViewPath()
    {
        return $this->_viewPath;
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
}
