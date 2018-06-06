<?php

namespace Gear\Modules\Resources\Plugins;

use Gear\Modules\Resources\Library\GResourcePlugin;

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
    protected $_allowedExtensions = ['js'];
    protected $_mappingFolder = null;
    protected $_typeResource = 'js';
    protected $_mime = 'text/javascript';
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
        $opt = [];
        foreach ($options as $param => $value) {
            $opt[] = $param . "=\"$value\"";
        }
        return '<script src="' . $url . '" ' . implode(' ', $opt) . "></script>\n";
    }
}
