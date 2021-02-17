<?php

namespace Gear\Modules\Resources\Plugins;

use Gear\Modules\Resources\Library\GResourcePlugin;

/**
 * Публикатор js-файлов
 *
 * @package Gear Framework
 *
 * @property bool mapFile
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GCssResourcesPlugin extends GResourcePlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected array $_allowedExtensions = ['css'];
    protected string $_basePath = 'Resources/Css';
    protected $_mappingFolder = null;
    protected ?string $_mime = 'text/css';
    protected ?string $_typeResource = 'css';
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
        if ($forceValue = $this->forceCache()) {
            $url .= "?{$forceValue}";
        }
        return '<link href="' . $url . '" ' . implode(' ', $opt) . " rel=\"stylesheet\" />\n";
    }
}
