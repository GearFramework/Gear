<?php

namespace Gear\Interfaces\Http;

use Gear\Interfaces\Services\PluginInterface;

/**
 * Интерфейс плагинов для работы с web-сервером и окружением
 *
 * @property string requestUri
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ServerInterface extends PluginInterface
{
}