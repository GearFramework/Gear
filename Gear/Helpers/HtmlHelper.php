<?php

namespace Gear\Helpers;

use Gear\Core;
use Gear\Interfaces\StaticFactoryInterface;
use Gear\Library\GHelper;
use Gear\Models\Html\GHtmlDiv;
use Gear\Traits\Factory\StaticFactoryTrait;

/**
 * Хелпер для работы с HTML
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class HtmlHelper extends GHelper implements StaticFactoryInterface
{
    /* Traits */
    use StaticFactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_factoryProperties = [
        'div' => [
            'class' => '\Gear\Models\Html\GHtmlDiv',
        ],
    ];
    protected static $_model = [
        'div' => [
            'class' => '\Gear\Models\Html\GHtmlDiv',
        ],
    ];
    /* Public */

    /**
     * Возвращает параметры по-умолчанию создаваемых объектов
     *
     * @param array $properties
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public static function getFactoryProperties(array $properties = []): array
    {
        $tag = isset($properties['tag']) ? $properties['tag'] : 'div';
        return array_replace_recursive(static::$_factoryProperties[$tag], $properties);
    }

    /**
     * Возвращает div-элемент
     *
     * @param string $id
     * @param string $class
     * @return GHtmlDiv
     * @since 0.0.2
     * @version 0.0.2
     */
    public static function helpDiv(string $id = '', string $class = ''): GHtmlDiv
    {
        $properties = ['tag' => 'div'];
        if ($id) {
            $properties['id'] = $id;
        }
        if ($class) {
            $properties['class'] = $class;
        }
        return self::factory($properties);
    }

    /**
     * Возвращает сформированный урл
     *
     * @param string $controller
     * @param string $api
     * @param array $params
     * @param bool $rewriteOn
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpUrl(
        string $controller = '',
        string $api = '',
        array $params = [],
        bool $rewriteOn = false
    ): string {
        foreach ($params as $name => &$value) {
            $value = "$name=" . urlencode($value);
        }
        unset($value);
        if ($rewriteOn) {
            $params = implode('&', $params);
            $url = '/' . ($controller ? "{$controller}/" : '') . ($api ? "a/{$api}" : '') . ($params ? "?{$params}" : '');
        } else {
            if ($controller) {
                array_unshift($params, "r={$controller}" . ($api ? "/a/{$api}" : ''));
            }
            $params = implode('&', $params);
            $url = '/' . ($params ? "?{$params}" : '');
        }
        return $url;
    }
}
