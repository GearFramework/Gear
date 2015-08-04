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
        'installationPaths' =>
        [
            'modules' => '\gear\modules',
            'components' => '\gear\components\gear',
            'plugins' => '\gear\plugins\gear',
            'helpers' => '\gear\helpers',
        ],
        'urlApi' => 'https://api.github.com',
        'repositories' => ['https://www.github.com/GearFramework']
    ];
    /* Public */

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
        if ($this->isInstalled('components', $component))
        {
            echo "Component $component already installed\n";
            return false;
        }
        if (($found = $this->isExists($component . '-component')) === false)
        {
            echo "Component $component not found\n";
            return false;
        }
        list($url, $owner, $repo) = $found;
        echo "Get listing / [";
        $listing = $this->getListing($repo, '/');
        if (!$listing) { echo "ERROR]\n"; return false; }
        echo "OK]\n";
        $toPath = $this->getInstallationPath('components', $component);
        echo "Create component directory $toPath [";
        if (!@mkdir($toPath)) { echo "ERROR]\n"; return false; }
        echo "OK]\n";
        echo "Download component to $toPath...\n";
        return $this->downloadResource($listing, $toPath, $repo);
    }

    public function downloadResource($listing, $toPath, $repo)
    {
        $result = true;
        foreach($listing as $list)
        {
            if ($list->type === 'file')
            {
                echo $list->path . " download file " . $list->download_url . " [";
                if (!@file_put_contents($toPath . '/' . $list->name, file_get_contents($list->download_url)))
                {
                    $result = false;
                    echo "ERROR]\n", "Download from " . $list->download_url . " error\n";
                    break;
                }
                echo "OK]\n";
            }
            else
            {
                $dir = $toPath . '/' . $list->name;
                echo "Create directory $dir [";
                if (!@mkdir($dir)) { $result = false; echo "ERROR]\n"; break; }
                echo "OK]\n";
                $listingDir = $this->getListing($repo, $list->path);
                echo "Get listing " . $list->path . " [";
                if (!$listingDir) { echo "ERROR]\n"; $result = false; break; }
                echo "OK]\n";
                echo "Download folder to $dir...\n";
                if (!$this->downloadResource($listingDir, $dir, $repo)) { $result = false; break; }
            }
        }
        return $result;
    }

    public function getInstallationPath($type, $name)
    {
        return Core::resolvePath($this->installationPaths[$type] . '/' . $name);
    }

    public function getListing($resource, $path)
    {
        $result = Core::app()->http->get
        (
            str_replace('/{+path}', $path, $resource->contents_url),
            [],
            ['UserAgent' => $this->userAgent],
            [$this, 'callbackResponse']
        );
        return is_object($result) && (isset($result->error) || isset($result->message)) ? false : $result;
    }

    public function isExists($resource)
    {
        $found = false;
        foreach($this->repositories as $url)
        {
            echo "Search $resource in $url [";
            $owner = substr($url, strrpos($url, '/') + 1);
            $result = Core::app()->http->get
            (
                $this->urlApi . '/repos/' . $owner . '/' . $resource,
                [],
                ['UserAgent' => $this->userAgent],
                [$this, 'callbackResponse']
            );
            if (is_object($result) && !isset($result->message))
            {
                $found = [$url, $owner, $result];
                echo "FOUND]\n";
                break;
            }
            echo "NOT FOUND]\n";
        }
        return $found;
    }

    public function isInstalled($type, $name) { return file_exists($this->getInstallationPath($type, $name)); }

    public function callbackResponse($response)
    {
        $result = $response->error ? $response : json_decode($response);
        return !$result ? $response : $result;
    }

    public function installPlugins($module)
    {

    }
}
