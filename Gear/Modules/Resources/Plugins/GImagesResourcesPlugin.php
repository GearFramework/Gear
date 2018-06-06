<?php

namespace Gear\Modules\Resources\Plugins;

use Gear\Modules\Resources\Library\GResourcePlugin;

/**
 * Публикатор файлов-картинок
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GImagesResourcesPlugin extends GResourcePlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_allowedExtensions = ['png', 'jpg', 'jpeg'];
    protected $_mappingFolder = null;
    protected $_typeResource = 'image';
    protected $_mime = ['image/png', 'image/jpeg'];
    protected $_basePath = 'Resources/Js';
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
        return $url;
    }
}
