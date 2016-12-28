<?php

namespace gear\plugins\templater;

use gear\library\GPlugin;

/**
 * Плагин для рендеринга шаблонов (шаблонизатор)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GView extends GPlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /**
     * @var bool $_initialized содержит состояние инициализации класса сервиса
     */
    protected static $_initialized = false;
    /* Public */
    /**
     * Отображение шаблона
     *
     * @param $template
     * @param array $context
     * @param bool $buffered
     * @throws \InvalidTemplateException
     * @return bool|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function render($template, array $context = [], bool $buffered = false)
    {
        if ($template instanceof \Closure) {
            $template = $template($context, $buffered);
        }
        if ($template instanceof \gear\library\GTemplate) {
            $result = $template->render($context, $buffered);
        } else if (!is_string($template)) {
            throw self::exceptionInvalidTemplate(['template' => $template]);
        } else {
            if (!preg_match('/(\\\\|\/)/', $template))
                $template = $this->owner->viewPath . '/' . $template;
            $template = \gear\Core::resolvePath($template);
            if (!preg_match('/\.phtml$/', $template))
                $template .= '.phtml';
            $result = $this->renderFile($template, $context, $buffered);
        }
        return $result;
    }

    /**
     * Подключение .phtml файла с шаблоном отображения
     *
     * @param string $__render__filePath__
     * @param array $__render__context__
     * @param bool $__render__buffered__
     * @throws \FileNotFoundException
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function renderFile(string $__render__filePath__, array $__render__context__ = [], bool $__render__buffered__ = false): string
    {
        if (!file_exists($__render__filePath__) || !is_readable($__render__filePath__)) {
            throw self::exceptionFileNotFound(['filePath' => $__render__filePath__]);
        }
        if ($__render__context__) {
            extract($__render__context__);
        }
        $__result__view__rendered__ = '';
        if ($__render__buffered__) {
            $__old__data__ = null;
            $__buffer__is__started = false;
            if (!empty(ob_get_status())) {
                $__old__data__ = ob_get_contents();
                @ob_end_clean();
                $__buffer__is__started = true;
            }
            ob_start();
            require $__render__filePath__;
            $__result__view__rendered__ = ob_get_contents();
            $__buffer__is__started ? print $__old__data__ : ob_end_clean();
        } else {
            require $__render__filePath__;
        }
        return $__result__view__rendered__;
    }
}