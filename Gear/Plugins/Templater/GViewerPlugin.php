<?php

namespace Gear\Plugins\Templater;

use Gear\Library\GPlugin;

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
class GViewerPlugin extends GPlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Отображение шаблона
     *
     * @param $template
     * @param array $context
     * @param bool $buffered
     * @return void|string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function render($template, array $context = [], bool $buffered = false)
    {
        if ($template instanceof \Closure) {
            $template = $template($context, $buffered);
        }
        if ($template instanceof \Gear\Library\GTemplate) {
            $result = $template->build($context);
        } elseif (!is_string($template)) {
            throw self::InvalidTemplateException(['template' => $template]);
        } else {
            if (isset($this->owner->viewsMap[$template])) {
                $template = $this->owner->viewsMap[$template];
            }
            if (!preg_match('/(\\\\|\/)/', $template)) {
                $template = $this->owner->viewPath . '/' . $template;
            }
            $template = \gear\Core::resolvePath($template);
            if (!preg_match('/\.phtml$/', $template)) {
                $template .= '.phtml';
            }
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
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function renderFile(string $__render__filePath__, array $__render__context__ = [], bool $__render__buffered__ = false): string
    {
        if (!file_exists($__render__filePath__) || !is_readable($__render__filePath__)) {
            throw self::FileNotFoundException(['file' => $__render__filePath__]);
        }
        if ($__render__context__) {
            extract($__render__context__);
        }
        $__result__view__rendered__ = '';
        if ($__render__buffered__) {
            $__old__data__ = null;
            $__buffered__ = false;
            if (ob_get_status()) {
                $__buffered__ = true;
                $__old__data__ = ob_get_contents();
                ob_clean();
            } else {
                ob_start();
            }
            require $__render__filePath__;
            $__result__view__rendered__ = ob_get_contents();
            ob_clean();
            if ($__buffered__ && $__old__data__) {
                echo $__old__data__;
            }
            if (!$__buffered__){
                ob_end_flush();
            }
        } else {
            require $__render__filePath__;
        }
        return $__result__view__rendered__;
    }
}
