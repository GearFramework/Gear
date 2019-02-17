<?php

namespace Psr\Log;

/**
 * PSR-3
 *
 * Уровни протоколирования
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class LogLevel
{
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const DEBUG     = 'debug';
    const EMERGENCY = 'emergency';
    const ERROR     = 'error';
    const INFO      = 'info';
    const NOTICE    = 'notice';
    const WARNING   = 'warning';
}
