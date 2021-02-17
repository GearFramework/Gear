<?php

namespace Gear\Modules\Resources\Interfaces;

use Gear\Interfaces\DirectoryInterface;
use Gear\Interfaces\FileInterface;
use Gear\Modules\Resources\GResourcesModule;

/**
 * Интерфейс публикатора ресурсов
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
interface ResourcePluginInterface
{
    const RESET_CACHE_BY_TIME = 'time';
    const RESET_CACHE_BY_ENV_VARIABLE = 'env';

    /**
     * Возвращает значение для сброса кэша браузера
     *
     * @since 0.0.2
     * @version 0.0.2
     * @return string|null
     */
    public function forceCache(): ?string;

    /**
     * Возвращает ресурс соответствующего указанному хэшу
     *
     * @param string $hash
     * @return string|FileInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function get(string $hash);

    /**
     * Генерирует html для вставки на страницу
     *
     * @param string $url
     * @param array $options
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function makeHtml(string $url, array $options = []): string;

    /**
     * Маппит ресурс в доступную для веб-сервера папку и возвращает урл-ресурса
     *
     * @param string|FileInterface $resource
     * @param bool $compile
     * @return string
     * @since 0.0.1
     * @version 0.0.2
     */
    public function mapping($resource, bool $compile = false);

    /**
     * Публикация ресурса, возвращает html-код для вставки на страницу
     *
     * @param string|FileInterface $resource
     * @param array $options
     * @param bool $compile
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function publicate($resource, array $options = [], bool $compile = false): string;
}
