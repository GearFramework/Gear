<?php

namespace gear\installer\components;
use gear\Core;
use gear\library\GComponent;

class GInstallerComponent extends GComponent
{
    /* Traits */
    /* Const */
    const INVALID_TYPE_INSTALL = 'Invalid type`s resource installing';
    const INVALID_TYPE_UPDATE = 'Invalid type`s resource updating';

    const MODULE_ALREADY = 'Module :moduleName already installed';
    const COMPONENT_ALREADY = 'Component :componentName already installed';
    const PLUGIN_ALREADY = 'Plugin :pluginName already installed';
    const HELPER_ALREADY = 'Helper :helperName already installed';
    const RESOURCE_ALREADY = ':resourceType :resourceName already installed';

    const RESOURCE_NOT_INSTALLED = 'Updating :resourceType :resourceName not installed';
    const RESOURCE_INSTALLED_PATH = ':resourceType :resourceName installed into :dirName';

    const MODULE_NOT_FOUND = 'Module :moduleName not found';
    const COMPONENT_NOT_FOUND = 'Component :componentName not found';
    const PLUGIN_NOT_FOUND = 'Plugin :pluginName not found';
    const HELPER_NOT_FOUND = 'Helper :helperName not found';
    const RESOURCE_NOT_FOUND = ':resourceType :resourceName not found';

    const MODULE_DOWNLOAD = 'Download module :moduleName to :dirName';
    const COMPONENT_DOWNLOAD = 'Download component :componentName to :dirName';
    const PLUGIN_DOWNLOAD = 'Download plugin :pluginName to :dirName';
    const HELPER_DOWNLOAD = 'Download helper :helperName to :dirName';
    const RESOURCE_DOWNLOAD = 'Download :resourceType :resourceName to :dirName';
    const RESOURCE_UPDATE = 'Update :resourceType :resourceName in :dirName';
    const RESOURCE_NOT_NEED_UPDATE = ':resourceType :resourceName does not need to be updated';

    const GET_LISTING = 'Get listing :dirName';
    const CREATE_DIR = 'Create directory :dirName';
    const DOWNLOAD_FILE = 'Download file :fileName';
    const DOWNLOAD_DIR = 'Download directory :dirName';
    const RESOURCE_SEARCH = 'Search :resourceName in :url';
    const RESOURCE_SEARCH_DATABASE = 'Search :resourceName in database url :url';
    const LOAD_SETTINGS_FILE = 'Load SETTINGS file';
    const LOCAL_VERSION = 'Local version [:version]';
    const REMOTE_VERSION = 'Remote version [:version]';

    const STATUS_ERROR = ' [ERROR]';
    const STATUS_OK = ' [OK]';
    const STATUS_DONE = ' [DONE]';
    const STATUS_YES = ' [YES]';
    const STATUS_NO = ' [NO]';
    const STATUS_NONE = ' [NONE]';
    const STATUS_FOUND = ' [FOUND]';
    const STATUS_NOT_FOUND = ' [NOT FOUND]';
    /* Private */
    /* Protected */
    protected static $_defaultProperties =
    [
        'userAgent' => 'Gear-Framework',
        'installationPaths' =>
        [
            'module' => '\gear\modules',
            'component' => '\gear\components\gear',
            'plugin' => '\gear\plugins\gear',
            'helper' => '\gear\helpers',
        ],
        'urlApi' => 'https://api.github.com',
        'repositories' => ['https://www.github.com/GearFramework'],
        'temp' => '\gear\temp',
        'dbResources' => '\gear\installer\RESOURCES.db',
    ];
    protected $_resourceSettings = null;
    protected $_installedResources = [];
    /* Public */

    /**
     * ”становка указанного ресурса
     *
     * @access public
     * @param string $resource
     * @return bool
     */
    public function installResource($resource)
    {
        list($type, $resourceName) = explode('/', $resource);
        $type = strtolower($type);
        if (!in_array($type, ['module', 'component', 'plugin', 'helper']))
            $this->e(self::INVALID_TYPE_INSTALL);
        $method = 'install' . ucfirst($type);
        if (method_exists($this, $method))
            return $this->$method($resourceName);

        if ($this->isInstalled($type, $resourceName))
            $this->e(self::RESOURCE_ALREADY, ['resourceType' => ucfirst($type), 'resourceName' => $resourceName]);
        if (($found = $this->isExists($resourceName, $type)) === false)
            $this->e(self::RESOURCE_NOT_FOUND, ['resourceType' => ucfirst($type), 'resourceName' => $resourceName]);
        list($url, $owner, $repo) = $found;
        $this->log(self::GET_LISTING, ['dirName' => '/'], false);
        $listing = $this->getListing($repo, '/');
        if (!$listing)
            $this->e(self::STATUS_ERROR);
        $this->log(self::STATUS_OK);
        $toPath = $this->getInstallationPath($type, $resourceName, $listing);
        if (!file_exists($toPath))
        {
            $this->log(self::CREATE_DIR, ['dirName' => $toPath], false);
            if (!@mkdir($toPath))
                $this->e(self::STATUS_ERROR);
            $this->log(self::STATUS_OK);
        }
        $this->log(self::RESOURCE_DOWNLOAD, ['resourceType' => $type, 'resourceName' => $resourceName, 'dirName' => $toPath]);
        $result = $this->downloadInstallingResource($listing, $toPath, $repo);
        $this->_writeDb($resourceName, $url);
        return $result;
    }

    /**
     * ќбновление указанного ресурса
     *
     * @access public
     * @param string $resource
     * @return bool
     */
    public function updateResource($resource)
    {
        list($type, $resourceName) = explode('/', $resource);
        $type = strtolower($type);
        if (!in_array($type, ['module', 'component', 'plugin', 'helper']))
            $this->e(self::INVALID_TYPE_UPDATE);
        $method = 'update' . ucfirst($type);
        if (method_exists($this, $method))
            return $this->$method($resourceName);

        if (!$this->isInstalled($type, $resourceName))
            $this->e(self::RESOURCE_NOT_INSTALLED, ['resourceType' => $type, 'resourceName' => $resourceName]);
        if (($found = $this->isExists($resourceName, $type)) === false)
            $this->e(self::RESOURCE_NOT_FOUND, ['resourceType' => ucfirst($type), 'resourceName' => $resourceName]);
        list($url, $owner, $repo) = $found;
        $this->log(self::GET_LISTING, ['dirName' => '/'], false);
        $listing = $this->getListing($repo, '/');
        if (!$listing)
            $this->e(self::STATUS_ERROR);
        $this->log(self::STATUS_OK);
        $toPath = $this->getInstallationPath($type, $resourceName, $listing);
        $this->log(self::RESOURCE_INSTALLED_PATH, ['resourceType' => ucfirst($type), 'resourceName' => $resourceName, 'dirName' => $toPath]);
        if (!$this->checkRequireUpdating($toPath))
            $this->e(self::RESOURCE_NOT_NEED_UPDATE, ['resourceType' => ucfirst($type), 'resourceName' => $resourceName]);
        $this->log(self::RESOURCE_UPDATE, ['resourceType' => $type, 'resourceName' => $resourceName, 'dirName' => $toPath]);
        return $this->downloadUpdatingResource($listing, $toPath, $repo);

    }

    public function checkRequireUpdating($installedPath)
    {
        if (is_array($this->_resourceSettings) && isset($this->_resourceSettings['version']))
        {
            $this->log(self::REMOTE_VERSION, ['version' => $this->_resourceSettings['version']]);
            $settings = file_exists($installedPath . '/SETTINGS') ? @file_get_contents($installedPath . '/SETTINGS') : null;
            if ($settings)
            {
                $settings = parse_ini_string($settings);
                if (is_array($settings) && isset($settings['version']))
                {
                    $this->log(self::LOCAL_VERSION, ['version' => $settings['version']]);
                    return !($settings['version'] === $this->_resourceSettings['version']);
                }
            }
            $this->log(self::LOCAL_VERSION, ['version' => 'NONE']);
        }
        else
            $this->log(self::REMOTE_VERSION, ['version' => 'NONE']);
        return true;
    }

    /**
     * «агрузка указанного ресурса
     *
     * @access public
     * @param array $listing
     * @param string $toPath
     * @param object $repo
     * @return bool
     */
    public function downloadInstallingResource($listing, $toPath, $repo)
    {
        $this->log(self::DOWNLOAD_DIR, ['dirName' => $toPath]);
        $result = true;
        foreach($listing as $list)
        {
            if ($list->type === 'file')
            {
                $this->log(self::DOWNLOAD_FILE, ['fileName' => $list->download_url], false);
                if (!@file_put_contents($toPath . '/' . $list->name, file_get_contents($list->download_url)))
                    $this->e(self::STATUS_ERROR);
                $this->log(self::STATUS_OK);
            }
            else
            {
                $dir = $toPath . '/' . $list->name;
                $this->log(self::CREATE_DIR, ['dirName' => $dir], false);
                if (!@mkdir($dir))
                    $this->e(self::STATUS_ERROR);
                $this->log(self::STATUS_OK);
                $listingDir = $this->getListing($repo, $list->path);
                $this->log(self::GET_LISTING, ['dirName' => $list->path], false);
                if (!$listingDir)
                    $this->e(self::STATUS_ERROR);
                $this->log(self::STATUS_OK);
                $this->downloadInstallingResource($listingDir, $dir, $repo);
            }
        }
        return $result;
    }

    /**
     * ќбновление-загрузка указанного ресурса
     *
     * @access public
     * @param array $listing
     * @param string $toPath
     * @param object $repo
     * @return bool
     */
    public function downloadUpdatingResource($listing, $toPath, $repo)
    {
        $this->log(self::DOWNLOAD_DIR, ['dirName' => $toPath]);
        $result = true;
        foreach($listing as $list)
        {
            if ($list->type === 'file')
            {
                $this->log(self::DOWNLOAD_FILE, ['fileName' => $list->download_url], false);
                if (file_exists($toPath . '/' . $list->name))
                    @unlink($toPath . '/' . $list->name);
                if (!@file_put_contents($toPath . '/' . $list->name, file_get_contents($list->download_url)))
                    $this->e(self::STATUS_ERROR);
                $this->log(self::STATUS_OK);
            }
            else
            {
                $dir = $toPath . '/' . $list->name;
                $this->log(self::CREATE_DIR, ['dirName' => $dir], false);
                if (!file_exists($dir))
                {
                    if (!@mkdir($dir))
                        $this->e(self::STATUS_ERROR);
                    $this->log(self::STATUS_OK);
                }
                $listingDir = $this->getListing($repo, $list->path);
                $this->log(self::GET_LISTING, ['dirName' => $list->path], false);
                if (!$listingDir)
                    $this->e(self::STATUS_ERROR);
                $this->log(self::STATUS_OK);
                $this->downloadUpdatingResource($listingDir, $dir, $repo);
            }
        }
        return $result;
    }

    /**
     * ѕолучение локального пути дл€ установки ресурса
     *
     * @access public
     * @param string $type
     * @param string $name
     * @return string
     */
    public function getInstallationPath($type, $name, array $listing = null)
    {
        $path = Core::resolvePath($this->installationPaths[$type] . '/' . $name);
        if ($listing)
        {
            $settings = $this->getSettings($listing);
            if ($settings && isset($settings['namespace']) && $settings['namespace'])
                $path = Core::resolvePath($settings['namespace']);
        }
        return $path;
    }

    /**
     * ¬озвращает массив настроек ресурса из файла SETTINGS если таковой существует в репозитории
     * ресурса
     *
     * @access public
     * @param array $listing
     * @return array|null
     */
    public function getSettings($listing)
    {
        if ($this->_resourceSettings !== null)
            return $this->_resourceSettings;
        $this->log(self::LOAD_SETTINGS_FILE, [], false);
        foreach($listing as $list)
        {
            if ($list->name === 'SETTINGS')
            {
                $settings = @file_get_contents($list->download_url);
                if ($settings)
                {
                    $this->_resourceSettings = parse_ini_string($settings);
                    $this->log(self::STATUS_OK);
                }
                else
                {
                    $this->_resourceSettings = [];
                    $this->log(self::STATUS_ERROR);
                }
                break;
            }
        }
        if ($this->_resourceSettings === null)
            $this->log(self::STATUS_NOT_FOUND);
        return $this->_resourceSettings;
    }

    /**
     * ¬озвращает список файлов и папок указанного репозитоори€ по указанному пути
     *
     * @access public
     * @param object $resource
     * @param string $path
     * @return bool|array
     */
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

    /**
     * ѕроизводит поиск указанного ресурса по списку репозиториев, возвращает найденный репозиторий, либо false
     *
     * @access public
     * @param string $resource
     * @param string $type
     * @return array|bool
     */
    public function isExists($resource, $type)
    {
        $found = false;
        /* ѕоиск в базе установленных ресурсов */
        if (isset($this->_installedResources[$resource]))
        {
            $this->log(self::RESOURCE_SEARCH_DATABASE, ['resourceName' => $resource, 'url' => $this->_installedResources[$resource]], false);
            $found = $this->_find($this->_installedResources[$resource], $resource . '-' . $type);
            if ($found)
            {
                $this->log(self::STATUS_FOUND);
                return $found;
            }
        }
        /* ѕоиск в списке репозиториев */
        foreach($this->repositories as $url)
        {
            $this->log(self::RESOURCE_SEARCH, ['resourceName' => $resource, 'url' => $url], false);
            $found = $this->_find($url, $resource . '-' . $type);
            if ($found)
            {
                $this->log(self::STATUS_FOUND);
                break;
            }
        }
        if (!$found)
            $this->log(self::STATUS_NOT_FOUND);
        return $found;
    }

    private function _find($url, $resource)
    {
        $found = false;
        $owner = substr($url, strrpos($url, '/') + 1);
        $result = Core::app()->http->get
        (
            $this->urlApi . '/repos/' . $owner . '/' . $resource,
            [],
            ['UserAgent' => $this->userAgent],
            [$this, 'callbackResponse']
        );
        if (is_object($result) && !isset($result->message))
            $found = [$url, $owner, $result];
        return $found;
    }

    /**
     * ¬озвращает true, если указанный ресурс установлен
     *
     * @access public
     * @param string $type
     * @param string $name
     * @return bool
     */
    public function isInstalled($type, $name)
    {
        $path = $this->getInstallationPath($type, $name);
        return file_exists($path) && count(scandir($path)) && isset($this->_installedResources[$name]);
    }

    /**
     * ќбработчик ответа от сервера
     *
     * @access public
     * @param object|string $response
     * @return mixed
     */
    public function callbackResponse($response)
    {
        $result = $response->error ? $response : json_decode($response);
        return !$result ? $response : $result;
    }

    /**
     * ¬ывод на экран сообщений
     *
     * @access public
     * @param string $message
     * @param array $params
     * @param bool|true $newLine
     * @return void
     */
    public function log($message, array $params = [], $newLine = true)
    {
        foreach($params as $param => $value)
            $message = str_replace(':' . $param, $value, $message);
        echo $message . ($newLine ? "\n" : '');
    }

    private function _loadDb()
    {
        $db = Core::resolvePath($this->dbResources);
        if (file_exists($db))
            $this->_installedResources = unserialize(file_get_contents($db));
        else
        {
            $this->_installedResources = [];
            $this->_flushDb();
        }
    }

    private function _writeDb($resourceName, $repository)
    {
        $this->_installedResources[$resourceName] = $repository;
        $db = Core::resolvePath($this->dbResources);
        file_put_contents($db, serialize($this->_installedResources));
    }

    private function _flushDb()
    {
        $db = Core::resolvePath($this->dbResources);
        file_put_contents($db, serialize($this->_installedResources));
    }

    public function onInstalled()
    {
        $this->_loadDb();
        return true;
    }
}
