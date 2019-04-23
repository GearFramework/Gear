<?php

namespace Gear\Models\Ffmpeg;

use Gear\Interfaces\DependentInterface;
use Gear\Library\Io\Filesystem\GFile;

/**
 * Модель видео-файла
 *
 * @package Gear Framework
 *
 * @property array info
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class FFMpegMovie extends GFile implements DependentInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_info = null;
    /* Public */

    public function getDuration()
    {
        $info = $this->info;
    }

    public function getInfo()
    {
        if (!$this->_info) {
            $out = shell_exec($this->shellCommand . ' -i ' . $this->path);
        }
        return $this->_info;
    }

    public function setInfo(array $info)
    {
        $this->_info = $info;
    }
}
