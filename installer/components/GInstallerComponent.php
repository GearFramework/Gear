<?php

namespace gear\installer\components;
use gear\Core;
use gear\library\GComponent;

class GInstallerComponent extends GComponent
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_defaultProperties =
    [
        'userAgent' => 'Gear-Framework',
        'installationPath' => '\gear\components\gear',
    ];
    protected $_urlApi = 'https://api.github.com';
    /* Public */

    public function getUrlApi() { return $this->_urlApi; }

    public function installResource($resource)
    {
        list($type, $name) = explode('/', $resource);
        $method = 'install' . ucfirst(strtolower($type));
        if (!method_exists($this, $method))
            $this->e('Invalid type installing');
        return $this->$method($name);
    }

    public function updateResource($resource)
    {
        list($type, $name) = explode('/', $resource);
        $method = 'update' . ucfirst(strtolower($type));
        if (!method_exists($this, $method))
            $this->e('Invalid type updating');
        return $this->$method($name);
    }

    public function installModules($module)
    {
        $result = Core::app()->http->get
        (
            $this->urlApi . '/repos/GearFramework/' . $module . '.module',
            [],
            [],
            [$this, 'callbackResponse']
        );
    }

    public function installComponents($component)
    {
        if (!$this->isExists($component . '.component'))
        {
            echo "ERROR]\n", "Component $component.component not found\n";
            return false;
        }
        echo "OK]\nGet listing [";
        $listing = $this->getListing($component . '.component');
        if ($listing)
        {
            echo "ERROR]\n";
            return false;
        }
        echo "OK]\n";
        foreach($listing as $list)
        {
            echo $list->path . " download [OK]\n";
        }
        return true;
    }

    public function getListing($resource)
    {
        $result = Core::app()->http->get
        (
            $this->urlApi . '/repos/GearFramework/' . $resource . '/contents',
            [],
            ['UserAgent' => $this->userAgent],
            [$this, 'callbackResponse']
        );
        return is_object($result) && (isset($result->error) || isset($result->message)) ? false : $result;
    }

    public function isExists($resource)
    {
        echo "Search $resource [";
        $result = Core::app()->http->get
        (
            $this->urlApi . '/repos/GearFramework/' . $resource,
            [],
            ['UserAgent' => $this->userAgent],
            [$this, 'callbackResponse']
        );
        return !is_object($result) || isset($result->message) ? false : true;
    }

    public function callbackResponse($response)
    {
        $result = $response->error ? $response : json_decode($response);
        return !$result ? $response : $result;
    }

    public function installPlugins($module)
    {

    }
}
