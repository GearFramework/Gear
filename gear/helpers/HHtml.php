<?php

namespace gear\helpers;

use gear\Core;
use gear\library\GHelper;

class HHtml extends GHelper
{
    public static function helpUrl(string $controller = '', string $api = '', array $params = [])
    {
        foreach($params as $name => &$value) {
            $value = "$name=" . urlencode($value);
        }
        unset($value);
        if (Core::app()->controllers->rewrite) {
            $params = implode('&', $params);
            $url = '/' . ($controller ? $controller . '/' : '') . ($api ? "a/$api/" : '') . ($params ? "?$params" : '');
        } else {
            if ($controller) {
                array_unshift($params, "r=$controller" . ($api ? "/a/$api" : ''));
            }
            $params = implode('&', $params);
            $url = '/' . ($params ? "?$params" : '');
        }
        return $url;
    }
}
