<?php

namespace Gear\Helpers;

use Gear\Core;
use Gear\Library\GHelper;

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
class HtmlHelper extends GHelper
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Возвращает сформированный урл
     *
     * @param string $controller
     * @param string $api
     * @param array $params
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function helpUrl(string $controller = '', string $api = '', array $params = []): string
    {
        foreach ($params as $name => &$value) {
            $value = "$name=" . urlencode($value);
        }
        unset($value);
        if (Core::app()->{Core::props('routerName')}->rewrite) {
            $params = implode('&', $params);
            $url = '/' . ($controller ? $controller . '/' : '') . ($api ? "a/$api/" : '') . ($params ? "?$params" : '');
        } else {
            if ($controller) {
                array_unshift($params, "r=$controller" . ($api ? "/a/$api" : ''));
            }
            $params = implode('&', $params);
            $url = 'index.php' . ($params ? "?$params" : '');
        }
        return $url;
    }
}