<?php

namespace gear\helpers;

use gear\Core;

class HHtml extends GHelper
{
    public static function helpUrl(string $controller = '', string $api = '', array $params = [])
    {
        $p = [];
        foreach($params as $name => $value) {
            $p[] = "$name=" . urlencode($value);
        }
        if (Core::app()->controllers->rewrite) {
            $params = implode('&', $p);
            $url = '/' . ($controller ? $controller . '/' : '') . ($api ? "/a/$api/" : '') . ($params ? "?$params" : '');
        } else {
            if ($api) {
                array_unshift($params, "a=$api");
            }
            if ($controller) {
                array_unshift($params, "r=$controller");
            }
            $params = implode('&', $p);
            $url = '/' . ($params ? "?$params" : '');
        }
        return $url;
    }
}
