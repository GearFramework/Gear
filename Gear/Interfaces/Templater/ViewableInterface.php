<?php

namespace Gear\Interfaces\Templater;

use Stringable;

/**
 * Интерфейс отображаемых объектов
 *
 * @property string     $viewerName
 * @property Stringable $viewLayout
 * @property Stringable $viewPath
 * @property array      $viewsMap
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ViewableInterface
{
    /**
     * Отображение шаблона
     *
     * @param   string|Stringable|TemplateInterface   $template
     * @param   array                                 $context
     * @param   null|ViewOptionsInterface             $options
     * @return  bool|string
     */
    public function render(
        string|Stringable|TemplateInterface $template,
        array $context = [],
        ?ViewOptionsInterface $options = null,
    ): bool|string;
}
