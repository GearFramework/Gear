<?php

namespace Gear\Modules\Resources\Library;

use Gear\Interfaces\CacheInterface;
use Gear\Interfaces\DirectoryInterface;
use Gear\Interfaces\FileInterface;
use Gear\Interfaces\FileSystemInterface;
use Gear\Library\GPlugin;
use Gear\Library\Io\Filesystem\GFile;
use Gear\Modules\Resources\GResourcesModule;
use Gear\Modules\Resources\Interfaces\ResourcePluginInterface;

/**
 * Публикатор ресурсов
 *
 * @package Gear Framework
 *
 * @property array allowedExtensions
 * @property string basePath
 * @property string controller
 * @property bool forceResetCache
 * @property string forceResetCacheType
 * @property null|string forceResetCacheVariable
 * @property bool hashingName
 * @property bool mapFile
 * @property string|DirectoryInterface mappingFolder
 * @property string mime
 * @property GResourcesModule owner
 * @property string typeResource
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GResourcePlugin extends GPlugin implements ResourcePluginInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected array $_allowedExtensions = [];
    protected string $_basePath = 'Resources';
    protected string $_controller = '\Gear\Resources\Publicate';
    protected bool $_forceResetCache = false;
    protected string $_forceResetCacheType = self::RESET_CACHE_BY_TIME;
    protected ?string $_forceResetCacheVariable = null;
    protected bool $_hashingName = true;
    protected $_mappingFolder = null;
    protected ?string $_mime = null;
    protected ?string $_typeResource = null;
    /* Public */

    /**
     * Кэширует ресурс и возвращает url-для доступа к нему
     *
     * @param CacheInterface $cache
     * @param FileInterface $resource
     * @param bool $compile
     * @return string
     * @since 0.0.1
     * @version 0.0.2
     */
    public function caching(CacheInterface $cache, FileInterface $resource, bool $compile)
    {
        $key = md5($resource);
        if ($cache->exists($key)) {
            if ($compile) {
                $data = $this->owner->view->renderFile($resource, [], true);
            } else {
                $data = file_get_contents($resource);
            }
            $result = $cache->set($key, $data);
        } else {
            $result = $cache->add($key, file_get_contents($resource));
        }
        return $result ? 'index.php?r=' . str_replace('\\', '_', $this->controller) . '/get&hash=' . $key . '&type=' . $this->typeResource : '';
    }

    /**
     * Возвращает значение для сброса кэша браузера
     *
     * @since 0.0.2
     * @version 0.0.2
     * @return string|null
     */
    public function forceCache(): ?string
    {
        $forceValue = null;
        if ($this->forceResetCache === true) {
            if ($this->forceResetCacheType === self::RESET_CACHE_BY_TIME) {
                $forceValue = (string)(time() + microtime(true));
            } elseif (
                $this->forceResetCacheType === self::RESET_CACHE_BY_ENV_VARIABLE
                && $this->forceResetCacheVariable
            ) {
                $forceValue = getenv($this->forceResetCacheVariable);
            }
        }
        return $forceValue;
    }

    /**
     * Возвращает ресурс соответствующего указанному хэшу
     *
     * @param string $hash
     * @return string|FileInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function get(string $hash)
    {
        if (($cache = $this->owner->cache)) {
            $resource = $this->getFromCache($cache, $hash);
        } else {
            $tempFile = $this->getTempFile();
            $records = $tempFile->content(function($data) { return json_decode($data, true); });
            if (!isset($records[$hash])) {
                throw static::ResourcesException('Resource with the corresponding hash <{hash}> is not found', ['hash' => $hash]);
            }
            $resource = new GFile(['path' => $records[$hash]]);
        }
        return $resource;
    }

    /**
     * Возвращает массив расширений, которые может обрабатывать плагин
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAllowedExtensions(): array
    {
        return $this->_allowedExtensions;
    }

    /**
     * Возвращает базовый путь расположения ресурсов
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getBasePath(): string
    {
        return $this->_basePath;
    }

    /**
     * Возвращает путь к контроллеру
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getController(): string
    {
        return $this->_controller;
    }

    public function getForceResetCache(): bool
    {
        return $this->_forceResetCache;
    }

    public function getForceResetCacheType(): string
    {
        return $this->_forceResetCacheType;
    }

    public function getForceResetCacheVariable(): ?string
    {
        return $this->_forceResetCacheVariable;
    }

    /**
     * Возвращает контент ресурса из кэша
     *
     * @param CacheInterface $cache
     * @param string $hash
     * @return string
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getFromCache(CacheInterface $cache, string $hash): string
    {
        $data = $cache->get($hash);
        return $data ?: '';
    }

    /**
     * Возвращает true или false определяющие будет ли при отражении ресурса его оригинальное имя
     * файла хэшироваться или нет
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHashingName(): bool
    {
        return $this->_hashingName;
    }

    /**
     * Возвращает название папки (доступной для веб-сервера), в которую отражаются файлы ресурсов
     *
     * @return string|DirectoryInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getMappingFolder() {
        return $this->_mappingFolder;
    }

    /**
     * Возвращает общий mime-тип ресурса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getMime(): string
    {
        return $this->_mime;
    }

    /**
     * Возвращает временный файл
     *
     * @return FileInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getTempFile(): FileInterface
    {
        /**
         * @var FileInterface $tempFile
         */
        $tempFile = new GFile(['path' => $this->owner->namespace . '\temp\resources.json']);
        if (!$tempFile->exists()) {
            if (!file_exists($tempFile->dirname)) {
                if (!@mkdir($tempFile->dirname)) {
                    throw static::ResourcesException('Temp directory <{tempDir}> not created', ['tempDir' => $tempFile->dirname]);
                }
                chmod($tempFile->dirname, 0770);
            }
            $tempFile->create();
        }
        return $tempFile;
    }

    /**
     * Возвращает общий тип ресурса, обрабатываемый плагином
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getTypeResource(): string
    {
        return $this->_typeResource;
    }

    /**
     * Возвращает true, если указанный ресурс является валидным для данного плагина
     *
     * @param string|FileInterface $resource
     * @return bool
     * @since 0.0.1
     * @version 0.0.2
     */
    public function isAllowedResource($resource): bool
    {
        $ext = pathinfo($resource, PATHINFO_EXTENSION);
        return in_array(strtolower($ext), $this->allowedExtensions, true);
    }

    /**
     * Возвращает true, если указанный тип ресурса является валидным для данного плагина
     *
     * @param string $typeResource
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isAllowedTypeResource(string $typeResource): bool
    {
        return $this->typeResource === (string)$typeResource;
    }

    /**
     * Генерирует html для вставки на страницу
     *
     * @param string $url
     * @param array $options
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function makeHtml(string $url, array $options = []): string;

    /**
     * Маппит ресурс в доступную для веб-сервера папку и возвращает урл-ресурса
     *
     * @param string|FileInterface $resource
     * @param bool $compile
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function mapping($resource, bool $compile = false): string
    {
        /**
         * @var FileSystemInterface $resource
         */
        $mappingFolder = $_SERVER['DOCUMENT_ROOT'] .'/' . $this->mappingFolder;
        if (!is_writable($mappingFolder)) {
            throw self::FileSystemException('Mapping directory <{folder}> is not writable', ['folder' => $mappingFolder]);
        }
        $mappingFile = $this->hashingName ? md5($resource) . '.' . $resource->extension() : $resource->basename;
        $mappingResource = $mappingFolder . '/' . $mappingFile;
        if (!file_exists($mappingResource) || $resource->mtime() > filemtime($mappingResource)) {
            if ($compile) {
                $data = $this->owner->view->renderFile($resource, [], true);
                file_put_contents($mappingResource, $data);
            } else {
                copy($resource, $mappingResource);
            }
            if ($this->mapFile && file_exists($resource->path . '.map')) {
                $mappingResourceMap = $mappingFolder . '/' . $resource->basename . '.map';
                copy($resource->path . '.map', $mappingResourceMap);
            }
        }
        return $this->urlMapped ? $this->urlMapped . '/' . $mappingFile : $this->mappingFolder . '/' . $mappingFile;
    }

    /**
     * Подготовка ресурса
     *
     * @param string|FileInterface $resource
     * @return FileInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function prepareResource($resource): FileInterface
    {
        if (is_string($resource)) {
            if (!preg_match('/^([a-z]\:|\/|\\\\)/i', $resource)) {
                $resource = $this->basePath . '/' . $resource;
            }
        }
        return $this->owner->prepareResource($resource);
    }

    /**
     * Публикация ресурса, возвращает html-код для вставки на страницу
     *
     * @param string|FileInterface|array $resource
     * @param array $options
     * @param bool $compile
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function publicate($resource, array $options = [], bool $compile = false): string
    {
        if (is_array($resource)) {
            $html = [];
            foreach($resource as $res) {
                $html[] = $this->publicate($resource, $options, $compile);
            }
            return implode("\n", $html);
        } else {
            $resource = $this->prepareResource($resource);
            if ($this->mappingFolder) {
                $url = $this->mapping($resource, $compile);
            } elseif (($cache = $this->owner->cache)) {
                $url = $this->caching($cache, $resource, $compile);
            } else {
                $hash = md5($resource);
                $tempFile = $this->getTempFile();
                if ($tempFile->isEmpty()) {
                    $records = [$hash => (string)$tempFile];
                    $hashExists = false;
                } else {
                    $records = $tempFile->content(function($data) { return json_decode($data, true); });
                    if (!isset($records[$hash])) {
                        $records[$hash] = (string)$tempFile;
                        $hashExists = false;
                    }
                }
                if (!$hashExists) {
                    $tempFile->content = json_encode($records);
                }
                $url = '/index.php?r=' . str_replace('\\', '_', $this->controller) . '/get&hash=' . $hash . '&type=' . $this->typeResource;
            }
            return $this->makeHtml($url, $options);
        }
    }

    /**
     * Отправляет ресурс клиенту
     *
     * @param string|FileInterface $resource
     * @since 0.0.1
     * @version 0.0.2
     */
    public function send($resource)
    {
        if (ob_get_status()) {
            ob_end_clean();
        }
        $mime = $resource instanceof FileInterface ? $resource->mime : $this->mime;
        header('Content-Type: ' . $mime);
        header('Content-Length: ', filesize($resource));
        echo (string)$resource;
    }

    /**
     * Устанавливает базовый путь расположения ресурсов
     *
     * @param string $path
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setBasePath(string $path)
    {
        $this->_basePath = $path;
    }

    /**
     * Устанавливает путь к контроллеру
     *
     * @param string $controller
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setController(string $controller)
    {
        $this->_controller = $controller;
    }

    public function setForceResetCache(bool $forceResetCache)
    {
        $this->_forceResetCache = $forceResetCache;
    }

    public function setForceResetCacheType(string $forceResetCacheType)
    {
        $this->_forceResetCacheType = $forceResetCacheType;
    }

    public function setForceResetCacheVariable(?string $forceResetCacheVariable)
    {
        $this->_forceResetCacheVariable = $forceResetCacheVariable;
    }

    /**
     * Устанавливает true или false определяющие будет ли при отражении ресурса его оригинальное имя
     * файла хэшироваться или нет
     *
     * @param bool $hashingName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setHashingName(bool $hashingName)
    {
        $this->_hashingName = $hashingName;
    }

    /**
     * Установка папки (доступной для веб-сервера), в которую будут отражаться файлы ресурсов
     *
     * @param string|DirectoryInterface $folder
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setMappingFolder($folder) {
        $this->_mappingFolder = $folder;
    }

    /**
     * Устанавливает общий mime-тип ресурса
     *
     * @param string $mime
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setMime(string $mime)
    {
        $this->_mime = $mime;
    }

    /**
     * Устанавливает общий тип ресурса, обрабатываемый плагином
     *
     * @param string $typeResource
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setTypeResource(string $typeResource)
    {
        $this->_typeResource = $typeResource;
    }
}
