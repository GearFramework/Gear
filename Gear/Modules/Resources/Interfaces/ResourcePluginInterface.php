<?php

namespace Gear\Modules\Resources\Interfaces;

use Gear\Interfaces\FileInterface;

/**
 * Интерфейс публикатора ресурсов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ResourcePluginInterface
{
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
