<?php

namespace Gear\Enums;

/**
 * Режимы запуска приложения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
enum RunMode: string
{
    case Development = 'development';
    case Stage       = 'stage';
    case Production  = 'production';
}
