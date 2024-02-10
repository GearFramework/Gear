<?php

namespace Gear\Interfaces\Templater;

use Gear\Interfaces\Io\Filesystem\FileInterface;
use Stringable;

/**
 * Интерфейс шаблонизаторов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ViewerInterface
{
    /**
     * Отображение шаблона
     *
     * @param   string|FileInterface|TemplateInterface  $template
     * @param   array                                   $context
     * @param   null|ViewOptionsInterface               $options
     * @return  bool|string
     */
    public function render(
        string|FileInterface|TemplateInterface $template,
        array $context = [],
        ?ViewOptionsInterface $options = null,
    ): bool|string;

    /**
     * Подключение .phtml файла с шаблоном отображения
     *
     * @param   string|FileInterface      $filePath
     * @param   array                     $context
     * @param   null|ViewOptionsInterface $options
     * @return  bool|string
     */
    public function renderFile(
        string|FileInterface $filePath,
        array $context = [],
        ?ViewOptionsInterface $options = null,
    ): bool|string;

    /**
     * Обработка схемы подключения .phtml файлов и их отображения
     *
     * @param   array                     $schema
     * @param   array                     $context
     * @param   null|ViewOptionsInterface $options
     * @return  bool|string
     */
    public function renderSchema(
        array $schema,
        array $context = [],
        ?ViewOptionsInterface $options = null
    ): bool|string;
}
