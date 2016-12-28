<?php

namespace gear\modules\resources\plugins;

use gear\modules\resources\library\GResourcePlugin;

/**
 * Публикатор js-файлов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GJsResourcesPlugin extends GResourcePlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_viewPath = '\gear\modules\resources\views';
    protected $_allowedExtensions = ['js'];
    protected $_mappingFolder = null;
    protected $_template = 'js.phtml';
    /* Public */

    /**
     * Генерирует html для вставки на страницу
     *
     * @param string $url
     * @param array $options
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function makeHtml(string $url, array $options = []): string
    {
        return $this->owner->view($this->viewPath . '/' . $this->template, ['url' => $url, 'options' => $options], true);
    }
}
