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
    const MODULE_NOT_FOUND = 'Module :moduleName not found';
    const COMPONENT_NOT_FOUND = 'Component :componentName not found';
    const PLUGIN_NOT_FOUND = 'Plugin :pluginName not found';
    const HELPER_NOT_FOUND = 'Helper :helperName not found';
    const MODULE_DOWNLOAD = 'Download module :moduleName to :dirName';
    const COMPONENT_DOWNLOAD = 'Download component :componentName to :dirName';
    const PLUGIN_DOWNLOAD = 'Download plugin :pluginName to :dirName';
    const HELPER_DOWNLOAD = 'Download helper :helperName to :dirName';

    const GET_LISTING = 'Get listing :dirName';
    const CREATE_DIR = 'Create directory :dirName';
    const DOWNLOAD_FILE = 'Download file :fileName';
    const DOWNLOAD_DIR = 'Download directory :dirName';
    const RESOURCE_SEARCH = 'Search :resourceName in :url';
    const LOAD_SETTINGS_FILE = 'Load SETTINGS file';

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
            'modules' => '\gear\modules',
            'components' => '\gear\components\gear',
            'plugins' => '\gear\plugins\gear',
            'helpers' => '\gear\helpers',
        ],
        'urlApi' => 'https://api.github.com',
        'repositories' => ['https://www.github.com/GearFramework'],
        'temp' => '\gear\temp',
    ];
    protected $_resourceSettings = null;
    /* Public */

    /**
     * Установка указанного ресурса
     *
     * @access public
     * @param string $resource
     * @return bool
     */
    public function installResource($resource)
    {
        list($type, $name) = explode('/', $resource);
        $method = 'install' . ucfirst(strtolower($type));
        if (!method_exists($this, $method))
            $this->e(self::INVALID_TYPE_INSTALL);
        return $this->$method($name);
    }

    /**
     * Обновление указанного ресурса
     *
     * @access public
     * @param string $resource
     * @return bool
     */
    public function updateResource($resource)
    {
        list($type, $name) = explode('/', $resource);
        $method = 'update' . ucfirst(strtolower($type));
        if (!method_exists($this, $method))
            $this->e(self::INVALID_TYPE_UPDATE);
        return $this->$method($name);
    }

    /**
     * Установка указанного модуля
     *
     * @access public
     * @param string $component
     * @return bool
     */
    public function installModules($module)
    {
    }

    /**
     * Установка указанного компонента
     *
     * @access public
     * @param string $component
     * @return bool
     */
    public function installComponents($component)
    {
        if ($this->isInstalled('components', $component))
            $this->e(self::COMPONENT_ALREADY, ['componentName' => $component]);
        if (($found = $this->isExists($component . '-component')) === false)
            $this->e(self::COMPONENT_NOT_FOUND, ['componentName' => $component]);
        list($url, $owner, $repo) = $found;
        $this->log(self::GET_LISTING, ['dirName' => '/'], false);
        $listing = $this->getListing($repo, '/');
        if (!$listing)
            $this->e(self::STATUS_ERROR);
        $this->log(self::STATUS_OK);
        $toPath = $this->getInstallationPath('components', $component, $listing);
        if (!file_exists($toPath))
        {
            $this->log(self::CREATE_DIR, ['dirName' => $toPath], false);
            if (!@mkdir($toPath))
                $this->e(self::STATUS_ERROR);
            $this->log(self::STATUS_OK);
        }
        $this->log(self::COMPONENT_DOWNLOAD, [':componentName' => $component, 'dirName' => $toPath]);
        return $this->downloadResource($listing, $toPath, $repo);
    }

    /**
     * Установка указанного плагина
     *
     * @access public
     * @param string $plugin
     * @return bool
     */
    public function installPlugins($plugin)
    {

    }

    /**
     * Установка указанного хелпера
     *
     * @access public
     * @param string $helper
     * @return bool
     */
    public function installHelpers($helper)
    {

    }

    /**
     * Загрузка указанного ресурса
     *
     * @access public
     * @param array $listing
     * @param string $toPath
     * @param object $repo
     * @return bool
     */
    public function downloadResource($listing, $toPath, $repo)
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
                $this->downloadResource($listingDir, $dir, $repo);
            }
        }
        return $result;
    }

    /**
     * Получение локального пути для установки ресурса
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
     * Возвращает массив настроек ресурса из файла SETTINGS если таковой существует в репозитории
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
     * Возвращает список файлов и папок указанного репозитоория по указанному пути
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
     * Производит поиск указанного ресурса по списку репозиториев, возвращает найденный репозиторий, либо false
     *
     * @access public
     * @param string $resource
     * @return array|bool
     */
    public function isExists($resource)
    {
        $found = false;
        foreach($this->repositories as $url)
        {
            $this->log(self::RESOURCE_SEARCH, ['resourceName' => $resource, 'url' => $url], false);
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
                $this->log(self::STATUS_FOUND);
                break;
            }
        }
        if (!$found)
            $this->log(self::STATUS_NOT_FOUND);
        return $found;
    }

    /**
     * Возвращает true, если указанный ресурс установлен
     *
     * @access public
     * @param string $type
     * @param string $name
     * @return bool
     */
    public function isInstalled($type, $name)
    {
        $path = $this->getInstallationPath($type, $name);
        return file_exists($path) && count(scandir($path));
    }

    /**
     * Обработчик ответа от сервера
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
     * Вывод на экран сообщений
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
}
