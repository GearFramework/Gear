<?php

namespace Gear\Interfaces\Templater;

/**
 * Интерфейс шаблонов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface TemplateInterface
{
    /**
     * Функция рендеринга шаблона
     *
     * @param   array                     $context
     * @param   ViewOptionsInterface|null $options
     * @return  bool|string
     */
    public function render(array $context = [], ?ViewOptionsInterface $options = null): bool|string;
}
